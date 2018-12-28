<?php

namespace sommerce\helpers;


use sommerce\modules\admin\models\forms\EditPaymentMethodForm;
use yii\helpers\Html;
use Yii;
use common\models\stores\PaymentMethodsCurrency;
use common\models\stores\PaymentMethods;

/**
 * Class SettingsFormHelper
 * @package sommerce\helpers
 */
class SettingsFormHelper
{
    /**
     * @param EditPaymentMethodForm $storeMethod
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getMethodFormData(EditPaymentMethodForm $storeMethod): array
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

            switch ($settings['type']) {
                case 'input':
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::input(
                            'text', $storeMethod->getFormElementName($settings['name']),
                            $options[$settings['code']],
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-control',
                            ]
                        ) .
                        '</div>';
                    break;

                case 'checkbox':
                    $result[$key] =
                        '<div class="form-group">' .
                        '<label class="form-check-label">' .
                        Html::checkbox(
                            $storeMethod->getFormElementName($settings['name']),
                            $options[$settings['code']],
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-check-input',
                            ]
                        ) . ' ' .
                        Yii::t('admin', $settings['label']) .
                        '</label>';
                    break;

                case 'multi_input':
                    $result[$key] =
                        '<div id="multi_input_container_descriptions">';

                    $id = 1;
                    foreach ((array)$options[$settings['code']] as $description) {
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

                case 'select':
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::dropDownList(
                            $storeMethod->getFormElementName($settings['name']),
                            $options[$settings['code']],
                            $settings['options'],
                            [
                                'id' => 'editpaymentmethod-' . $settings['code'],
                                'class' => 'form-control',
                            ]
                        ) .
                        '</div>';
                    break;

                case 'textarea':
                    $result[$key] =
                        '<div class="form-group">' .
                        $commonLabel .
                        Html::textarea(
                            $storeMethod->getFormElementName($settings['name']),
                            $options[$settings['code']],
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
