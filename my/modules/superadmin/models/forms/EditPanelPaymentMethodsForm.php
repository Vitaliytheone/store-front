<?php

namespace superadmin\models\forms;

use common\helpers\CurrencyHelper;
use common\models\panels\PaymentMethods;
use common\models\panels\Project;
use common\models\panels\services\GetPanelAvailablePaymentMethodsService;
use common\models\panels\services\GetPanelPaymentMethodsService;
use common\models\panels\services\GetPaymentMethodsCurrencyService;
use yii\base\Model;
use Yii;
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
     * @param int $methodId
     * @param int $allow 1 - allow; 0 - disallow
     * @return bool
     * @throws \yii\db\Exception
     */
    public function changeAvailability(int $methodId, $allow = 1): bool
    {
        $range = [0, 1];
        $paymentMethod = PaymentMethods::findOne($methodId);
        if (!isset($paymentMethod) || !in_array($allow, $range)) {
            return false;
        }

        $users = (new Query())
            ->select(['id', 'payments'])
            ->from($this->_panel->db . '.users')
            ->all();

        foreach ($users as $user) {
            $payments = json_decode($user['payments'], true);

            if (!isset($payments[$methodId]) || (isset($payments[$methodId]) && $payments[$methodId] !== $allow)) {
                $payments[$methodId] = $allow;

                $paymentOptions = json_encode($payments);
                Yii::$app->db->createCommand()
                    ->update($this->_panel->db . '.users', ['payments' => $paymentOptions], ['id' => $user['id']])
                    ->execute();
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
