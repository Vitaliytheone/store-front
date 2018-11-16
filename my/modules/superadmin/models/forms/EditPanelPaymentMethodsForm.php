<?php

namespace superadmin\models\forms;

use common\helpers\CurrencyHelper;
use common\models\panels\PanelPaymentMethods;
use common\models\panels\Project;
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

        $paymentMethods = $this->getPaymentMethods();
        $currentMethods = ArrayHelper::index(PanelPaymentMethods::find()->andWhere([
            'panel_id' => $this->_panel->id
        ])->all(), 'method_id');

        $transaction = Yii::$app->db->beginTransaction();

        foreach ((array)$this->methods as $methodId => $value) {
            if (!empty($currentMethods[$methodId])) {
                unset($currentMethods[$methodId]);
                continue;
            }

            if (empty($paymentMethods[$methodId])) {
                continue;
            }

            $model = new PanelPaymentMethods();
            $model->method_id = $methodId;
            $model->panel_id = $this->_panel->id;
            $model->name = ArrayHelper::getValue($paymentMethods, [$methodId, 'method_name']);
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
            $paymentMethods = CurrencyHelper::getPaymentMethods();
            $panelPaymentMethods = Yii::$container->get(GetPanelPaymentMethodsService::class, [$this->_panel])->get();

            foreach ($paymentMethods as $method) {
                if (empty($panelPaymentMethods[$method['id']])) {
                    continue;
                }

                static::$paymentMethods[$method['id']] = [
                    'id' => $method['id'],
                    'method_name' => $method['method_name'],
                    'active' => (int)!empty($panelPaymentMethods[$method['id']])
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
            static::$paymentMethodsCurrency = ArrayHelper::index(static::$paymentMethodsCurrency, 'method_id', 'currency');
        }

        return static::$paymentMethodsCurrency;
    }

    /**
     * @return array
     */
    public function getPaymentMethodDropdown()
    {
        $options = [];
        $panelPaymentMethods = $this->getPanelPaymentMethods();
        $paymentMethodsCurrency = $this->getPaymentMethodsCurrency();
        $currencies = CurrencyHelper::getAvailableCurrencies();
        $methods = CurrencyHelper::getPaymentMethods();
        ArrayHelper::multisort($methods, 'method_name');

        $panelPaymentMethods = ArrayHelper::index($panelPaymentMethods, 'currency_id');

        foreach ($currencies as $currency) {
            foreach ((array)ArrayHelper::getValue($paymentMethodsCurrency, $currency, []) as $currencyMethods) {
                foreach ($currencyMethods as $methodId => $currencyMethod) {
                    if (!empty($panelPaymentMethods[$currencyMethod['id']])) {
                        continue;
                    }

                    $method = $methods[$methodId];
                    $group = Yii::t('superadmin', 'payments.add_payment_method_modal.select.available_for', [
                        'currency' => $currency
                    ]);

                    $options[$group][$method['currency_id']] = $method['method_name'];
                }
            }
        }

        return $options;
    }
}
