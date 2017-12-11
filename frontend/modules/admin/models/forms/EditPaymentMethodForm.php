<?php

namespace frontend\modules\admin\models\forms;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;
use common\models\stores\PaymentMethods;

/**
 * Class EditPaymentMethodForm
 * @package frontend\modules\admin\models\forms
 */
class EditPaymentMethodForm extends PaymentMethods
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
                        $apiUsername = ArrayHelper::getValue($details, 'username');
                        $details['username'] = trim($apiUsername);
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
     * Return payments method edit form data
     * @return mixed
     */
    public function getMethodFormData()
    {
        $method = $this->method;
        $details = $this->details;
        
        $getDetailsField = function ($field) use ($details){
            return ArrayHelper::getValue($details, $field);
        };
        
        $paymentsFormData = [

            self::METHOD_PAYPAL => [
                'icon' => '/img/paypal.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_username', 'placeholder' => '', 'name' => 'PaymentsForm[details][username]', 'value' => $getDetailsField('username'), 'label' => Yii::t('admin', 'settings.payments_paypal_username')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_password', 'placeholder' => '', 'name' => 'PaymentsForm[details][password]', 'value' => $getDetailsField('password'), 'label' => Yii::t('admin', 'settings.payments_paypal_password')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_signature', 'placeholder' => '', 'name' => 'PaymentsForm[details][signature]', 'value' => $getDetailsField('signature'), 'label' => Yii::t('admin', 'settings.payments_paypal_signature')],
                    ['tag' => 'input', 'type' => 'checkbox', 'name' => 'PaymentsForm[details][test_mode]', 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],

            self::METHOD_2CHECKOUT => [
                'icon' => '/img/2checkout.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'credit_card_number', 'placeholder' => '', 'name' => 'PaymentsForm[details][account_number]', 'value' => $getDetailsField('account_number'), 'label' => Yii::t('admin', 'settings.payments_2checkout_account_number')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'credit_card_word', 'placeholder' => '', 'name' => 'PaymentsForm[details][secret_word]', 'value' => $getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_2checkout_secret_word')],
                    ['tag' => 'input', 'type' => 'checkbox', 'id' => '', 'name' => 'PaymentsForm[details][test_mode]', 'value' => 1, 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],

            self::METHOD_BITCOIN => [
                'icon' => '/img/bitcoin.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'bitcoin_gateway_id', 'placeholder' => '', 'name' => 'PaymentsForm[details][gateway_id]', 'value' => $getDetailsField('gateway_id'), 'label' => Yii::t('admin', 'settings.payments_bitcoin_gateway_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'bitcoin_geteway_secret', 'placeholder' => '', 'name' => 'PaymentsForm[details][gateway_secret]', 'value' => $getDetailsField('gateway_secret'), 'label' => Yii::t('admin', 'settings.payments_bitcoin_gateway_secret')],
                ]
            ],
        ];

        return ArrayHelper::getValue($paymentsFormData, $method);
    }
}