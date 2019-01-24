<?php

namespace superadmin\models\forms;

use common\helpers\CurrencyHelper;
use common\models\panel\Users;
use common\models\panels\PaymentMethods;
use common\models\panels\PanelPaymentMethods;
use common\models\panels\PaymentMethodsCurrency;
use common\models\panels\Project;
use common\models\panels\services\GetPanelAvailablePaymentMethodsService;
use common\models\panels\services\GetPanelPaymentMethodsService;
use common\models\panels\services\GetPaymentMethodsCurrencyService;
use yii\base\Model;
use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class EditPanelPaymentMethodsForm
 * @package superadmin\models\forms
 */
class EditPanelPaymentMethodsForm extends Model
{
    /**
     * @var integer
     */
    public $currency_id;

    public $methods;

    /**
     * @var Project
     */
    private $_panel;

    /**
     * @var array
     */
    protected static $paymentMethods;

    /**
     * @var array
     */
    protected static $paymentMethodsCurrency;

    /**
     * @var array
     */
    protected static $panelPaymentMethods;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['methods'], 'safe'],
            [['currency_id'], 'integer'],
        ];
    }

    /**
     * Set project
     * @param Project $panel
     */
    public function setPanel(Project $panel)
    {
        $this->_panel = $panel;
    }

    /**
     * @param $db
     */
    public function setConnection(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $panelPaymentMethod = PanelPaymentMethods::findOne(['panel_id' => $this->_panel->id, 'currency_id' => $this->currency_id]);
        $currencyPaymentMethod = PaymentMethodsCurrency::findOne($this->currency_id);

        if ($panelPaymentMethod || !isset($currencyPaymentMethod)) {
            return false;
        }

        $paymentMethods = CurrencyHelper::getPaymentMethods();
        $paymentMethod = $paymentMethods[$currencyPaymentMethod->method_id];

        $name = !empty($paymentMethod['name']) ? $paymentMethod['name'] : $paymentMethod['method_name'];
        $model = new PanelPaymentMethods();
        $model->currency_id = $this->currency_id;
        $model->method_id = $currencyPaymentMethod->method_id;
        $model->panel_id = $this->_panel->id;
        $model->name = $name;
        $model->setOptions([]);

        if (!$model->save()) {
            $this->addError('methods', Yii::t('app/superadmin', 'panels.edit.payment_methods.error'));
            return false;
        }

        return true;
    }

    /**
     * @param int $methodId
     * @param int $allow 1 - allow; 0 - disallow
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function changeAvailability(int $methodId, $allow = 1): bool
    {
        $range = [Users::PAYMENT_METHOD_DISALLOW, Users::PAYMENT_METHOD_ALLOW];
        $paymentMethod = PaymentMethods::findOne($methodId);
        if (!isset($paymentMethod) || !in_array($allow, $range)) {
            return false;
        }

        $db = Yii::$app->db;
        Yii::$app->panel->setInstance($this->_panel);

        $users = Users::find()
            ->select([
                'id',
                'payments',
            ]);

        foreach ($users->batch(100) as $usersModels) {
            $update = [];
            foreach ($usersModels as $user) {
                /** @var Users $user */
                $payments = $user->getPayments();

                if (!isset($payments[$methodId]) || (isset($payments[$methodId]) && $payments[$methodId] !== $allow)) {
                    $payments[$methodId] = $allow;

                    $user->setPayments($payments);

                    $update[] = [
                        'id' => $user->id,
                        'payments' => $user->payments,
                    ];
                }
            }

            if (!empty($update)) {
                $sql = $db->createCommand()->batchInsert($this->_panel->db . '.' . Users::tableName(), [
                    'id',
                    'payments',
                ], $update)->rawSql;

                $db->createCommand($sql . ' ON DUPLICATE KEY UPDATE payments = VALUES(payments)')->execute();
            }
        }

        return true;
    }

    /**
     * Delete payment method
     * @param int $methodId
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deletePaymentMethod(int $methodId): bool
    {
        $payment = PanelPaymentMethods::findOne([
            'panel_id' => $this->_panel->id,
            'method_id' => $methodId,
        ]);

        if (!isset($payment)) {
            return false;
        }

        if (!$payment->delete()) {
            return false;
        }

        $db = Yii::$app->db;
        Yii::$app->panel->setInstance($this->_panel);

        $users = Users::find()
            ->select([
                'id',
                'payments',
            ]);

        foreach ($users->batch(100) as $usersModels) {
            $update = [];
            foreach ($usersModels as $user) {
                /** @var Users $user */
                $payments = $user->getPayments();

                if (array_key_exists($methodId, $payments)) {
                    unset($payments[$methodId]);
                    $user->setPayments($payments);

                    $update[] = [
                        'id' => $user->id,
                        'payments' => $user->payments,
                    ];
                }
            }

            if (!empty($update)) {
                $sql = $db->createCommand()->batchInsert($this->_panel->db . '.' . Users::tableName(), [
                    'id',
                    'payments',
                ], $update)->rawSql;

                $db->createCommand($sql . ' ON DUPLICATE KEY UPDATE payments = VALUES(payments)')->execute();
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'methods' => Yii::t('app/superadmin', 'providers.modal_edit_provider.provider_id'),
        ];
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getPaymentMethods()
    {
        if (null === static::$paymentMethods) {
            static::$paymentMethods = [];
            $panelPaymentMethods = $this->getPanelPaymentMethods();
            $paymentMethodsCurrency = $this->getPaymentMethodsCurrency();
            $paymentMethods = CurrencyHelper::getPaymentMethods();

            foreach ($panelPaymentMethods as $method) {
                $currency = ArrayHelper::getValue($paymentMethodsCurrency, $method['currency_id']);
                static::$paymentMethods[$method['currency_id']] = [
                    'id' => $method['method_id'],
                    'currency_id' => $method['currency_id'],
                    'method_name' => $paymentMethods[$method['method_id']]['method_name'],
                    'currency' => $currency['currency'],
                ];
            }
        }

        return static::$paymentMethods;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function getPanelPaymentMethods()
    {
        if (null === static::$panelPaymentMethods) {
            static::$panelPaymentMethods = Yii::$container->get(GetPanelPaymentMethodsService::class, [$this->_panel, 'name'])->get();
        }

        return static::$panelPaymentMethods;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function getPaymentMethodsCurrency()
    {
        if (null === static::$paymentMethodsCurrency) {
            static::$paymentMethodsCurrency = Yii::$container->get(GetPaymentMethodsCurrencyService::class, [$this->_panel])->get();
        }

        return static::$paymentMethodsCurrency;
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getPaymentMethodDropdown()
    {
        return Yii::$container->get(GetPanelAvailablePaymentMethodsService::class, [$this->_panel])->get();
    }
}
