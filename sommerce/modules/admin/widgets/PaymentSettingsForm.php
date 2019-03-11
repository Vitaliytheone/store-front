<?php

namespace sommerce\modules\admin\widgets;

use yii\base\Widget;
use sommerce\modules\admin\models\forms\EditPaymentMethodForm;
use common\models\sommerces\PaymentMethodsCurrency;
use Yii;
use yii\helpers\Html;
use common\models\sommerces\PaymentMethods;

/**
 * Class PaymentSettingsForm
 * @package sommerce\modules\admin\widgets
 */
class PaymentSettingsForm extends Widget
{
    /** @var EditPaymentMethodForm */
    public $paymentModel;

    /** @var string */
    public $submitUrl;

    /** @var string  */
    public $cancelUrl = '';

    /** @var string  */
    public $name = '';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        return $this->render('_payment_setting_form', [
            'paymentData' => $this->getMethodFormData($this->paymentModel),
            'submitUrl' => $this->submitUrl,
            'cancelUrl' => $this->cancelUrl,
            'name' => $this->name,
        ]);
    }

    /**
     * @param EditPaymentMethodForm $storeMethod
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getMethodFormData(EditPaymentMethodForm $storeMethod): array
    {
        /** @var PaymentMethodsCurrency $method */
        $method = $storeMethod->getStorePaymentMethodCurrency()->one();

        if (!isset($method->settings_form)) {
            /** @var PaymentMethods $method */
            $method = $storeMethod->getPaymentMethod()->one();
        }

        $settingForm = $method->getSettingsForm();
        $options = $storeMethod->getOptions();
        $result = [];

        foreach ($settingForm as $key => $settings) {
            $commonLabel = Html::label(Yii::t('admin', $settings['label']), 'editpaymentmethod-' . $settings['code']);

            $value = isset($options[$key]) ? $options[$key] : '';

            switch ($settings['type']) {
                case PaymentMethods::FIELD_TYPE_INPUT:
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::input(
                            'text', $storeMethod->getFormElementName($settings['name']),
                            $value,
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-control',
                            ]
                        ) .
                        '</div>';
                    break;

                case PaymentMethods::FIELD_TYPE_CHECKBOX:
                    $result[$key] =
                        '<div class="form-group">' .
                        '<label class="form-check-label">' .
                        Html::checkbox(
                            $storeMethod->getFormElementName($settings['name']),
                            $value,
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-check-input',
                            ]
                        ) . ' ' .
                        Yii::t('admin', $settings['label']) .
                        '</label>';
                    break;

                case PaymentMethods::FIELD_TYPE_MULTI_INPUT:
                    $result[$key] =
                        '<div id="multi_input_container_descriptions">';

                    $id = 1;
                    foreach ((array)$value as $description) {
                        $result[$key] .=
                            '<div class="form-group form-group-description">' .
                            '<span class="fa fa-times remove-description"></span>' .
                            Html::label(Yii::t('admin', $settings['label']), 'edit-payment-method-options-descriptions' . $id) .
                            Html::input('text', $storeMethod->getFormElementName($settings['name']) . '[]', $description, [
                                'class' => 'form-control multi-input-item',
                                'id' => 'edit-payment-method-options-descriptions' . $id,
                            ]) .
                            '</div>';

                        $id++;
                    }
                    $result[$key] .= '</div>' .
                        Html::a('<span>' . Yii::t('admin', 'settings.payments.multi_input.add_description') . '</span>', '#',
                            [
                                'class' => 'add-multi-input',
                                'data-name' => $storeMethod->getFormElementName($settings['name']) . '[]',
                                'data-label' => $settings['label'],
                                'data-class' => 'multi-input-item',
                                'data-id' => 'edit-payment-method-options-descriptions' . $id,
                            ]
                        );
                    break;

                case PaymentMethods::FIELD_TYPE_SELECT:
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::dropDownList(
                            $storeMethod->getFormElementName($settings['name']),
                            $value,
                            $settings['options'],
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-control',
                            ]
                        ) .
                        '</div>';
                    break;

                case PaymentMethods::FIELD_TYPE_TEXTAREA:
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::textarea(
                            $storeMethod->getFormElementName($settings['name']),
                            $value,
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-control',
                            ]
                        ) .
                        '</div>';
                    break;
            }
        }

        return $result;
    }
}
