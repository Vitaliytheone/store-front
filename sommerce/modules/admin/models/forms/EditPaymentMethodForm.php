<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;
use common\models\stores\PaymentMethods;
use yii\web\User;

/**
 * Class EditPaymentMethodForm
 * @package sommerce\modules\admin\models\forms
 */
class EditPaymentMethodForm extends PaymentMethods
{

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
            PaymentMethods::METHOD_PAYPAL => [
                'icon' => '/img/pg/paypal.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_username', 'placeholder' => '', 'name' => 'PaymentsForm[details][username]', 'value' => $getDetailsField('username'), 'label' => Yii::t('admin', 'settings.payments_paypal_username')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_password', 'placeholder' => '', 'name' => 'PaymentsForm[details][password]', 'value' => $getDetailsField('password'), 'label' => Yii::t('admin', 'settings.payments_paypal_password')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_signature', 'placeholder' => '', 'name' => 'PaymentsForm[details][signature]', 'value' => $getDetailsField('signature'), 'label' => Yii::t('admin', 'settings.payments_paypal_signature')],
                    ['tag' => 'input', 'type' => 'checkbox', 'name' => 'PaymentsForm[details][test_mode]', 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],
            PaymentMethods::METHOD_2CHECKOUT => [
                'icon' => '/img/pg/2checkout.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'credit_card_number', 'placeholder' => '', 'name' => 'PaymentsForm[details][account_number]', 'value' => $getDetailsField('account_number'), 'label' => Yii::t('admin', 'settings.payments_2checkout_account_number')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'credit_card_word', 'placeholder' => '', 'name' => 'PaymentsForm[details][secret_word]', 'value' => $getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_2checkout_secret_word')],
                    //['tag' => 'input', 'type' => 'checkbox', 'id' => '', 'name' => 'PaymentsForm[details][test_mode]', 'value' => 1, 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],
            PaymentMethods::METHOD_COINPAYMENTS => [
                'icon' => '/img/pg/coinpayments.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'coinpayments_merchant_id', 'placeholder' => '', 'name' => 'PaymentsForm[details][merchant_id]', 'value' => $getDetailsField('merchant_id'), 'label' => Yii::t('admin', 'settings.payments_coinpayments_merchant_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'coinpayments_ipn_secret', 'placeholder' => '', 'name' => 'PaymentsForm[details][ipn_secret]', 'value' => $getDetailsField('ipn_secret'), 'label' => Yii::t('admin', 'settings.payments_coinpayments_ipn_secret')],
                ]
            ],
            PaymentMethods::METHOD_WEBMONEY => [
                'icon' => '/img/pg/webmoney.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'webmoney_purse', 'placeholder' => '', 'name' => 'PaymentsForm[details][purse]', 'value' => $getDetailsField('purse'), 'label' => Yii::t('admin', 'settings.payments_webmoney_purse')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'webmoney_secret_key', 'placeholder' => '', 'name' => 'PaymentsForm[details][secret_key]', 'value' => $getDetailsField('secret_key'), 'label' => Yii::t('admin', 'settings.payments_webmoney_secret_key')],
                ]
            ],
            PaymentMethods::METHOD_YANDEX_MONEY => [
                'icon' => '/img/pg/yandex_money.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'yandex_money_wallet_number', 'placeholder' => '', 'name' => 'PaymentsForm[details][wallet_number]', 'value' => $getDetailsField('wallet_number'), 'label' => Yii::t('admin', 'settings.payments_yandex_money_wallet_number')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'yandex_money_secret_word', 'placeholder' => '', 'name' => 'PaymentsForm[details][secret_word]', 'value' => $getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_yandex_money_secret_word')],
                ]
            ],
            PaymentMethods::METHOD_PAGSEGURO => [
                'icon' => '/img/pg/pagseguro.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'pagseguro_email', 'placeholder' => '', 'name' => 'PaymentsForm[details][email]', 'value' => $getDetailsField('email'), 'label' => Yii::t('admin', 'settings.payments_pagseguro_email')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'pagseguro_token', 'placeholder' => '', 'name' => 'PaymentsForm[details][token]', 'value' => $getDetailsField('token'), 'label' => Yii::t('admin', 'settings.payments_pagseguro_token')],
                ]
            ],
            PaymentMethods::METHOD_FREE_KASSA => [
                'icon' => '/img/pg/free_kassa.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'free_kassa_merchant_id', 'placeholder' => '', 'name' => 'PaymentsForm[details][merchant_id]', 'value' => $getDetailsField('merchant_id'), 'label' => Yii::t('admin', 'settings.payments_free_kassa_merchant_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'free_kassa_secret_word', 'placeholder' => '', 'name' => 'PaymentsForm[details][secret_word]', 'value' => $getDetailsField('secret_word'), 'label' => Yii::t('admin', 'settings.payments_free_kassa_secret_word')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'free_kassa_secret_word2', 'placeholder' => '', 'name' => 'PaymentsForm[details][secret_word2]', 'value' => $getDetailsField('secret_word2'), 'label' => Yii::t('admin', 'settings.payments_free_kassa_secret_word2')],
                ]
            ],
            PaymentMethods::METHOD_PAYTR => [
                'icon' => '/img/pg/paytr.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paytr_merchant_id', 'placeholder' => '', 'name' => 'PaymentsForm[details][merchant_id]', 'value' => $getDetailsField('merchant_id'), 'label' => Yii::t('admin', 'settings.payments_paytr_merchant_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paytr_merchant_key', 'placeholder' => '', 'name' => 'PaymentsForm[details][merchant_key]', 'value' => $getDetailsField('merchant_key'), 'label' => Yii::t('admin', 'settings.payments_paytr_merchant_key')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paytr_merchant_salt', 'placeholder' => '', 'name' => 'PaymentsForm[details][merchant_salt]', 'value' => $getDetailsField('merchant_salt'), 'label' => Yii::t('admin', 'settings.payments_paytr_merchant_salt')],
                ]
            ],
            PaymentMethods::METHOD_PAYWANT => [
                'icon' => '/img/pg/paywant.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paywant_apiKey', 'placeholder' => '', 'name' => 'PaymentsForm[details][apiKey]', 'value' => $getDetailsField('apiKey'), 'label' => Yii::t('admin', 'settings.payments_paywant_apiKey')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paywant_apiSecret', 'placeholder' => '', 'name' => 'PaymentsForm[details][apiSecret]', 'value' => $getDetailsField('apiSecret'), 'label' => Yii::t('admin', 'settings.payments_paywant_apiSecret')],
                ]
            ],
            PaymentMethods::METHOD_BILLPLZ => [
                'icon' => '/img/pg/billplz.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'billplz_collectionId', 'placeholder' => '', 'name' => 'PaymentsForm[details][collectionId]', 'value' => $getDetailsField('collectionId'), 'label' => Yii::t('admin', 'settings.payments_billplz_collectionId')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'billplz_secret', 'placeholder' => '', 'name' => 'PaymentsForm[details][secret]', 'value' => $getDetailsField('secret'), 'label' => Yii::t('admin', 'settings.payments_billplz_secret')],
                ]
            ],
            PaymentMethods::METHOD_AUTHORIZE => [
                'icon' => '/img/pg/authorize.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'authorize_merchant_login_id', 'placeholder' => '', 'name' => 'PaymentsForm[details][merchant_login_id]', 'value' => $getDetailsField('merchant_login_id'), 'label' => Yii::t('admin', 'settings.payments_authorize_merchant_login_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'authorize_merchant_transaction_id', 'placeholder' => '', 'name' => 'PaymentsForm[details][merchant_transaction_id]', 'value' => $getDetailsField('merchant_transaction_id'), 'label' => Yii::t('admin', 'settings.payments_authorize_merchant_transaction_id')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'authorize_merchant_client_key', 'placeholder' => '', 'name' => 'PaymentsForm[details][merchant_client_key]', 'value' => $getDetailsField('merchant_client_key'), 'label' => Yii::t('admin', 'settings.payments_authorize_merchant_client_key')],
                    ['tag' => 'input', 'type' => 'checkbox', 'name' => 'PaymentsForm[details][test_mode]', 'checked' => $getDetailsField('test_mode') ? 'checked' : '', 'label' => Yii::t('admin', 'settings.payments_authorize_test_mode')],
                ]
            ],
        ];

        return ArrayHelper::getValue($paymentsFormData, $method);
    }

    /**
     * Change PG active status
     * @param $active
     * @return mixed
     */
    public function setActive($active)
    {
        $this->setAttribute('active', $active);
        $this->save();

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAYMENTS_PG_ACTIVE_STATUS_CHANGED, $this->id, $this->method);

        return $active;
    }


    /**
     * Change PG settings
     * @param $postData
     * @return bool
     */
    public function changeSettings($postData)
    {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAYMENTS_PG_SETTINGS_CHANGED, $this->id, $this->method);

        return true;
    }
}