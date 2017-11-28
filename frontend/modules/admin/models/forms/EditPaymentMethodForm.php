<?php

namespace frontend\modules\admin\models\forms;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class EditPaymentMethodForm
 * @package frontend\modules\admin\models\forms
 */
class EditPaymentMethodForm extends \common\models\stores\PaymentMethods
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => 'details',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    $details = $model->getAttribute('details');

                    // Prepare PayPal details
                    if ($model->method == $model::METHOD_PAYPAL) {
                        $apiUsername = ArrayHelper::getValue($details, 'api_username');
                        $details['api_username'] = trim($apiUsername);
                    }

                    return json_encode($details);
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_AFTER_FIND => 'details',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    return json_decode($model->getAttribute('details'),true);
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'PaymentsForm';
    }

    /**
     * Return payments methods list item data
     * @param $field
     * @return mixed
     */
    public function getMethodsListItemData($field)
    {
        $method = $this->method;

        $methodItemsData = [
            self::METHOD_PAYPAL => [
                'icon' => '/img/paypal.png',
                'title' => Yii::t('admin', 'settings.payments_method_paypal'),
                'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
            ],
            self::METHOD_2CHECKOUT => [
                'icon' => '/img/2checkout.png',
                'title' => Yii::t('admin', 'settings.payments_method_2checkout'),
                'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
            ],
            self::METHOD_BITCOIN => [
                'icon' => '/img/bitcoin.png',
                'title' => Yii::t('admin', 'settings.payments_method_bitcoin'),
                'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
            ],
        ];

        return ArrayHelper::getValue($methodItemsData, "$method.$field", $field);
    }

    /**
     * Return payment details field
     * @param $field
     * @return mixed
     */
    private function _getDetailsField($field)
    {
        return ArrayHelper::getValue($this->details, $field);
    }

    /**
     * Return payments method edit form data
     * @return mixed
     */
    public function getMethodFormData()
    {
        $method = $this->method;

        $paymentsFormData = [

            self::METHOD_PAYPAL => [
                'icon' => '/img/paypal.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_api_username', 'placeholder' => '', 'name' => 'PaymentsForm[details][api_username]', 'value' => $this->_getDetailsField('api_username'), 'label' => Yii::t('admin', 'settings.payments_paypal_username')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_api_password', 'placeholder' => '', 'name' => 'PaymentsForm[details][api_password]', 'value' => $this->_getDetailsField('api_password'), 'label' => Yii::t('admin', 'settings.payments_paypal_password')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_api_signature', 'placeholder' => '', 'name' => 'PaymentsForm[details][api_signature]', 'value' => $this->_getDetailsField('api_signature'), 'label' => Yii::t('admin', 'settings.payments_paypal_signature')],
                    ['tag' => 'input', 'type' => 'checkbox', 'name' => 'PaymentsForm[details][test_mode]', 'checked' => $this->_getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],

            self::METHOD_2CHECKOUT => [
                'icon' => '/img/2checkout.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'credit_card_number', 'placeholder' => '', 'name' => 'PaymentsForm[details][account_number]', 'value' => $this->_getDetailsField('account_number'), 'label' => Yii::t('admin', 'settings.payments_2checkout_account_number')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'credit_card_word', 'placeholder' => '', 'name' => 'PaymentsForm[details][secret_word]', 'value' => $this->_getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_2checkout_secret_word')],
                    ['tag' => 'input', 'type' => 'checkbox', 'id' => '', 'name' => 'PaymentsForm[details][test_mode]', 'value' => 1, 'checked' => $this->_getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],

            self::METHOD_BITCOIN => [
                'icon' => '/img/bitcoin.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'bitcoin_api_gateway_id', 'placeholder' => '', 'name' => 'PaymentsForm[details][api_gateway_id]', 'value' => $this->_getDetailsField('api_gateway_id'), 'label' => Yii::t('admin', 'settings.payments_bitcoin_gateway_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'bitcoin_geteway_secret', 'placeholder' => '', 'name' => 'PaymentsForm[details][api_gateway_secret]', 'value' => $this->_getDetailsField('api_gateway_secret'), 'label' => Yii::t('admin', 'settings.payments_bitcoin_gateway_secret')],
                ]
            ],
        ];

        return ArrayHelper::getValue($paymentsFormData, $method);
    }
}