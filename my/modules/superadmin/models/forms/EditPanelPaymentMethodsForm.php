<?php

namespace superadmin\models\forms;

use common\helpers\CurrencyHelper;
use common\models\panels\PanelPaymentMethods;
use common\models\panels\PaymentMethodsCurrency;
use common\models\panels\Project;
use common\models\panels\services\GetPanelAvailablePaymentMethodsService;
use common\models\panels\services\GetPanelPaymentMethodsService;
use common\models\panels\services\GetPaymentMethodsCurrencyService;
use yii\base\Model;
use Yii;
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
     * Save panel payment methods
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $paymentMethods = CurrencyHelper::getPaymentMethods();
        $currentMethods = ArrayHelper::index(PanelPaymentMethods::find()->andWhere([
            'panel_id' => $this->_panel->id
        ])->all(), 'currency_id');
        $paymentMethodsCurrency = ArrayHelper::index(PaymentMethodsCurrency::find()->all(), 'id');

        $transaction = Yii::$app->db->beginTransaction();

        if (!empty($this->currency_id)) {
            $this->methods[$this->currency_id] = 1;
        }

        foreach ((array)$this->methods as $currencyId => $value) {
            if (!empty($currentMethods[$currencyId])) {
                unset($currentMethods[$currencyId]);
                continue;
            }

            $currencyPaymentMethod = ArrayHelper::getValue($paymentMethodsCurrency, $currencyId);
            if (empty($currencyPaymentMethod) || empty($paymentMethods[$currencyPaymentMethod->method_id])) {
                continue;
            }

            $paymentMethod = $paymentMethods[$currencyPaymentMethod->method_id];

            $model = new PanelPaymentMethods();
            $model->currency_id = $currencyId;
            $model->method_id = $currencyPaymentMethod->method_id;
            $model->panel_id = $this->_panel->id;
            $model->name = $paymentMethod['method_name'];
            $model->setOptions([]);

            if (!$model->save()) {
                $this->addError('methods', Yii::t('app/superadmin', 'panels.edit.payment_methods.error'));
                return false;
            }
        }

        if (!empty($currentMethods)) {
            /**
             * @var $currentMethod PanelPaymentMethods
             */
            foreach ($currentMethods as $currentMethod) {
                $currentMethod->delete();
            }
        }

        $transaction->commit();

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
     */
    public function getPaymentMethods()
    {
        if (null === static::$paymentMethods) {
            static::$paymentMethods = [];
            $panelPaymentMethods = $this->getPanelPaymentMethods();
            $paymentMethodsCurrency = $this->getPaymentMethodsCurrency();

            foreach ($panelPaymentMethods as $method) {
                $currency = ArrayHelper::getValue($paymentMethodsCurrency, $method['currency_id']);
                static::$paymentMethods[$method['currency_id']] = [
                    'id' => $method['method_id'],
                    'currency_id' => $method['currency_id'],
                    'method_name' => $method['name'],
                    'currency' => $currency['currency'],
                ];
            }
        }

        return static::$paymentMethods;
    }

    /**
     * @return array
     */
    protected function getPanelPaymentMethods()
    {
        if (null === static::$panelPaymentMethods) {
            static::$panelPaymentMethods = Yii::$container->get(GetPanelPaymentMethodsService::class, [$this->_panel])->get();
        }

        return static::$panelPaymentMethods;
    }

    /**
     * @return array
     */
    protected function getPaymentMethodsCurrency()
    {
        if (null === static::$paymentMethodsCurrency) {
            static::$paymentMethodsCurrency = Yii::$container->get(GetPaymentMethodsCurrencyService::class, [$this->_panel])->get();
        }

        return static::$paymentMethodsCurrency;
    }

    /**
     * @return array
     */
    public function getPaymentMethodDropdown()
    {
        return Yii::$container->get(GetPanelAvailablePaymentMethodsService::class, [$this->_panel])->get();
    }
}
