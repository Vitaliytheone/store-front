<?php
namespace common\models\panels\services;

use yii\helpers\ArrayHelper;
use common\models\panels\PaymentMethods;
use common\models\panels\Project;
use yii\bootstrap\Html;
use Yii;

/**
 * Class GetCurrentPanelPaymentMethods
 * @package common\models\panels\services
 */
class GetCurrentPanelPaymentMethods {

    /**
     * @var Project
     */
    private $_panel;

    /**
     * GetPaymentMethodsService constructor.
     * @param Project $panel
     */
    public function __construct(Project $panel)
    {
        $this->_panel = $panel;
    }

    /**
     * @return array
     */
    public function get()
    {
        $methods = Yii::$container->get(GetPanelPaymentMethodsService::class, [$this->_panel])->get();
        $paymentMethods = Yii::$container->get(GetPaymentMethodsService::class, [$this->_panel])->get();
        $paymentMethodsCurrency = Yii::$container->get(GetPaymentMethodsCurrencyService::class, [$this->_panel])->get();
        $returnMethods = [];

        foreach ((array)$methods as $method) {
            $paymentMethod = ArrayHelper::getValue($paymentMethods, $method['method_id']);
            $currencyPaymentMethod = ArrayHelper::getValue($paymentMethodsCurrency, [$method['method_id'], $this->_panel->getCurrencyCode()]);
            if (empty($paymentMethod)) {
                continue;
            }

            $options = [];

            $settingsForm = !empty($currencyPaymentMethod['settings_form']) ? $currencyPaymentMethod['settings_form'] : $paymentMethod['settings_form'];

            // TODO: need to refactor
            foreach ($settingsForm as $field => $details) {
                $value = ArrayHelper::getValue($method['options'], $field);

                if (is_string($value)) {
                    $value = Html::encode($value);
                } else if (is_array($value)) {
                    $value = array_map(function($value) {return Html::encode((string)$value);}, $value);
                }

                if (PaymentMethods::FIELD_TYPE_MULTI_INPUT == $details['type']) {
                    $values = (array)$value;
                    $options[] = ArrayHelper::merge([
                        'values' => !empty($values) ? $values : [''],
                        'add_label' => Yii::t('admin/settings', 'payments.edit.add_multi_input', [
                            'name' => $details['name']
                        ])
                    ], $details);
                } elseif (PaymentMethods::FIELD_TYPE_COURSE == $details['type']) {
                    // TODO: Temporary commented
                    //if (!empty($currencyPaymentMethod['auto_exchange_rate']) && in_array($this->_panel->getCurrencyCode(), $currencyPaymentMethod['exchange_currency'])) {
                    $options[] = ArrayHelper::merge([
                        'value' => $value
                    ], $details);
                    //}
                } else {
                    $options[] = ArrayHelper::merge([
                        'value' => $value
                    ], $details);
                }
            }

            $returnMethods[] = [
                'id' => $method['id'],
                'method_id' => $method['method_id'],
                'method_name' => $paymentMethod['method_name'],
                'name' => $method['name'],
                'min' => $method['minimal'],
                'max' => $method['maximal'],
                'new_users' => $method['new_users'],
                'visibility' => $method['visibility'],
                'details' => [
                    'title' => $paymentMethod['method_name'],
                    'name' => $method['name'],
                    'minimal' => $method['minimal'],
                    'maximal' => $method['maximal'],
                    'new_users' => $method['new_users'],
                    'is_enabled_take_fee_from_user' => $paymentMethod['take_fee_from_user'],
                    'take_fee_from_user' => $method['take_fee_from_user'],
                    'description' => !empty($currencyPaymentMethod['settings_form_description']) ? $currencyPaymentMethod['settings_form_description'] : $paymentMethod['settings_form_description'],
                    'options' => $options,
                ]
            ];
        }

        return $returnMethods;
    }
}