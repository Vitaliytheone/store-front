<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StorePaymentMethods;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;
use common\models\stores\PaymentMethods;
use yii\web\User;

/**
 * Class EditPaymentMethodForm
 * @package sommerce\modules\admin\models\forms
 */
class EditPaymentMethodForm extends StorePaymentMethods
{

    // TODO rename to StoreEditPaymentMethodForm

    /**
     * @var User
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => 'options',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    $details = (array)$model->getAttribute('options');

                    foreach ($details as $key => $elem) {
                        if (is_string($elem)) {
                            $details[$key] = trim($elem);
                        }
                    }

                    // Prepare PayPal details
                    if ($model->getPaymentMethod()->one()->name == PaymentMethods::METHOD_PAYPAL) {
                        $apiUsername = ArrayHelper::getValue($details, 'username');
                        $details['username'] = trim($apiUsername);
                    }

                    return json_encode($details);
                },
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_AFTER_FIND => 'options',
                ],
                'value' => function ($event) {
                    /* @var $event \yii\base\Event */
                    /* @var $model $this */
                    $model = $event->sender;
                    return json_decode($model->getAttribute('options'),true);
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
     * Set current user
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Return current user
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Return payments method edit form data
     * @var $this StorePaymentMethods
     * @return mixed
     */
    public function getMethodFormData()
    {
        Yii::debug($this, 'Edit'); // TODO del
        $method = $this->getPaymentMethod()->one()->method_name;
        $options = $this->options;

        Yii::debug($options, '$options'); // TODO del
        
        $getDetailsField = function ($field) use ($options){
            /** @var $options array */
            return ArrayHelper::getValue($options, $field);
        };

        $paymentsFormData = [
            PaymentMethods::METHOD_PAYPAL => [
                'icon' => '/img/pg/paypal.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_username', 'placeholder' => '', 'name' => 'PaymentsForm[options][username]', 'value' => $getDetailsField('username'), 'label' => Yii::t('admin', 'settings.payments_paypal_username')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_password', 'placeholder' => '', 'name' => 'PaymentsForm[options][password]', 'value' => $getDetailsField('password'), 'label' => Yii::t('admin', 'settings.payments_paypal_password')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_signature', 'placeholder' => '', 'name' => 'PaymentsForm[options][signature]', 'value' => $getDetailsField('signature'), 'label' => Yii::t('admin', 'settings.payments_paypal_signature')],
                    ['tag' => 'input', 'type' => 'checkbox', 'name' => 'PaymentsForm[options][test_mode]', 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],
            PaymentMethods::METHOD_2CHECKOUT => [
                'icon' => '/img/pg/2checkout.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'credit_card_number', 'placeholder' => '', 'name' => 'PaymentsForm[options][account_number]', 'value' => $getDetailsField('account_number'), 'label' => Yii::t('admin', 'settings.payments_2checkout_account_number')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'credit_card_word', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret_word]', 'value' => $getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_2checkout_secret_word')],
                    //['tag' => 'input', 'type' => 'checkbox', 'id' => '', 'name' => 'PaymentsForm[options][test_mode]', 'value' => 1, 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],
            PaymentMethods::METHOD_COINPAYMENTS => [
                'icon' => '/img/pg/coinpayments.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'coinpayments_merchant_id', 'placeholder' => '', 'name' => 'PaymentsForm[options][merchant_id]', 'value' => $getDetailsField('merchant_id'), 'label' => Yii::t('admin', 'settings.payments_coinpayments_merchant_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'coinpayments_ipn_secret', 'placeholder' => '', 'name' => 'PaymentsForm[options][ipn_secret]', 'value' => $getDetailsField('ipn_secret'), 'label' => Yii::t('admin', 'settings.payments_coinpayments_ipn_secret')],
                ]
            ],
            PaymentMethods::METHOD_WEBMONEY => [
                'icon' => '/img/pg/webmoney.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'webmoney_purse', 'placeholder' => '', 'name' => 'PaymentsForm[options][purse]', 'value' => $getDetailsField('purse'), 'label' => Yii::t('admin', 'settings.payments_webmoney_purse')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'webmoney_secret_key', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret_key]', 'value' => $getDetailsField('secret_key'), 'label' => Yii::t('admin', 'settings.payments_webmoney_secret_key')],
                ]
            ],
            PaymentMethods::METHOD_YANDEX_MONEY => [
                'icon' => '/img/pg/yandex_money.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'yandex_money_wallet_number', 'placeholder' => '', 'name' => 'PaymentsForm[options][wallet_number]', 'value' => $getDetailsField('wallet_number'), 'label' => Yii::t('admin', 'settings.payments_yandex_money_wallet_number')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'yandex_money_secret_word', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret_word]', 'value' => $getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_yandex_money_secret_word')],
                ]
            ],
            PaymentMethods::METHOD_STRIPE => [
                'icon' => '/img/pg/stripe_logo.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'stripe_secret_key', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret_key]', 'value' => $getDetailsField('secret_key'), 'label' => Yii::t('admin', 'settings.payments_stripe_secret_key')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'stripe_public_key', 'placeholder' => '', 'name' => 'PaymentsForm[options][public_key]', 'value' => $getDetailsField('public_key'), 'label' => Yii::t('admin', 'settings.payments_stripe_public_key')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'stripe_webhook_secret', 'placeholder' => '', 'name' => 'PaymentsForm[options][webhook_secret]', 'value' => $getDetailsField('webhook_secret'), 'label' => Yii::t('admin', 'settings.payments_stripe_webhook_secret')]
                ]
            ],
            PaymentMethods::METHOD_YANDEX_CARDS => [
                'icon' => '/img/pg/yandex_money.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'yandex_money_wallet_number', 'placeholder' => '', 'name' => 'PaymentsForm[options][wallet_number]', 'value' => $getDetailsField('wallet_number'), 'label' => Yii::t('admin', 'settings.payments_yandex_money_wallet_number')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'yandex_money_secret_word', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret_word]', 'value' => $getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_yandex_money_secret_word')],
                ]
            ],
            PaymentMethods::METHOD_PAGSEGURO => [
                'icon' => '/img/pg/pagseguro.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'pagseguro_email', 'placeholder' => '', 'name' => 'PaymentsForm[options][email]', 'value' => $getDetailsField('email'), 'label' => Yii::t('admin', 'settings.payments_pagseguro_email')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'pagseguro_token', 'placeholder' => '', 'name' => 'PaymentsForm[options][token]', 'value' => $getDetailsField('token'), 'label' => Yii::t('admin', 'settings.payments_pagseguro_token')],
                ]
            ],
            PaymentMethods::METHOD_FREE_KASSA => [
                'icon' => '/img/pg/free_kassa.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'free_kassa_merchant_id', 'placeholder' => '', 'name' => 'PaymentsForm[options][merchant_id]', 'value' => $getDetailsField('merchant_id'), 'label' => Yii::t('admin', 'settings.payments_free_kassa_merchant_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'free_kassa_secret_word', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret_word]', 'value' => $getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_free_kassa_secret_word')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'free_kassa_secret_word2', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret_word2]', 'value' => $getDetailsField('secret_word2'), 'label' => Yii::t('admin', 'settings.payments_free_kassa_secret_word2')],
                ]
            ],
            PaymentMethods::METHOD_PAYTR => [
                'icon' => '/img/pg/paytr.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paytr_merchant_id', 'placeholder' => '', 'name' => 'PaymentsForm[options][merchant_id]', 'value' => $getDetailsField('merchant_id'), 'label' => Yii::t('admin', 'settings.payments_paytr_merchant_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paytr_merchant_key', 'placeholder' => '', 'name' => 'PaymentsForm[options][merchant_key]', 'value' => $getDetailsField('merchant_key'), 'label' => Yii::t('admin', 'settings.payments_paytr_merchant_key')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paytr_merchant_salt', 'placeholder' => '', 'name' => 'PaymentsForm[options][merchant_salt]', 'value' => $getDetailsField('merchant_salt'), 'label' => Yii::t('admin', 'settings.payments_paytr_merchant_salt')],
                ]
            ],
            PaymentMethods::METHOD_PAYWANT => [
                'icon' => '/img/pg/paywant.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paywant_apiKey', 'placeholder' => '', 'name' => 'PaymentsForm[options][apiKey]', 'value' => $getDetailsField('apiKey'), 'label' => Yii::t('admin', 'settings.payments_paywant_apiKey')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paywant_apiSecret', 'placeholder' => '', 'name' => 'PaymentsForm[options][apiSecret]', 'value' => $getDetailsField('apiSecret'), 'label' => Yii::t('admin', 'settings.payments_paywant_apiSecret')],
                ]
            ],
            PaymentMethods::METHOD_BILLPLZ => [
                'icon' => '/img/pg/billplz.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'billplz_collectionId', 'placeholder' => '', 'name' => 'PaymentsForm[options][collectionId]', 'value' => $getDetailsField('collectionId'), 'label' => Yii::t('admin', 'settings.payments_billplz_collectionId')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'billplz_secret', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret]', 'value' => $getDetailsField('secret'), 'label' => Yii::t('admin', 'settings.payments_billplz_secret')],
                ]
            ],
            PaymentMethods::METHOD_AUTHORIZE => [
                'icon' => '/img/pg/authorize.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'authorize_merchant_login_id', 'placeholder' => '', 'name' => 'PaymentsForm[options][merchant_login_id]', 'value' => $getDetailsField('merchant_login_id'), 'label' => Yii::t('admin', 'settings.payments_authorize_merchant_login_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'authorize_merchant_transaction_id', 'placeholder' => '', 'name' => 'PaymentsForm[options][merchant_transaction_id]', 'value' => $getDetailsField('merchant_transaction_id'), 'label' => Yii::t('admin', 'settings.payments_authorize_merchant_transaction_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'authorize_merchant_client_key', 'placeholder' => '', 'name' => 'PaymentsForm[options][merchant_client_key]', 'value' => $getDetailsField('merchant_client_key'), 'label' => Yii::t('admin', 'settings.payments_authorize_merchant_client_key')],
                    ['tag' => 'input', 'type' => 'checkbox', 'name' => 'PaymentsForm[options][test_mode]', 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_authorize_test_mode')],
                ]
            ],
            PaymentMethods::METHOD_MERCADOPAGO => [
                'icon' => '/img/pg/mercado_pago.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'mercadopado_client_id', 'placeholder' => '', 'name' => 'PaymentsForm[options][client_id]', 'value' => $getDetailsField('client_id'), 'label' => Yii::t('admin', 'settings.payments_mercadopago_client_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'mercadopado_secret', 'placeholder' => '', 'name' => 'PaymentsForm[options][secret]', 'value' => $getDetailsField('secret'), 'label' => Yii::t('admin', 'settings.payments_mercadopago_secret')],
                    ['tag' => 'input', 'type' => 'checkbox', 'name' => 'PaymentsForm[options][test_mode]', 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_mercadopago_test_mode')],
                ]
            ],
        ];

        return ArrayHelper::getValue($paymentsFormData, $method);
    }

    /**
     * Change PG active status
     * @param $active
     * @return mixed
     * @throws \Throwable
     */
    public function setActive($active)
    {
        $this->setAttribute('visibility', $active);
        $this->save();

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAYMENTS_PG_ACTIVE_STATUS_CHANGED, $this->id, $this->method_id);

        return $active;
    }


    /**
     * Change store_payment_methods settings
     * @param $postData
     * @return bool
     * @throws \Throwable
     */
    public function changeSettings($postData)
    {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAYMENTS_PG_SETTINGS_CHANGED, $this->id, $this->method_id);

        return true;
    }
}