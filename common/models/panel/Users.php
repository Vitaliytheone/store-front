<?php

namespace common\models\panel;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%users}}".
 *
 * @property int $id
 * @property string $login
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int $referrer_id
 * @property string $skype
 * @property string $key
 * @property int $hash_method
 * @property string $hash
 * @property int $hash_expiry_at
 * @property string $auth_key
 * @property string $balance
 * @property string $spent
 * @property int $date
 * @property int $lastlogin
 * @property string $lastip
 * @property string $apikey
 * @property string $ref_key
 * @property int $status
 * @property int $terms
 * @property int $timezone
 * @property string $lang
 * @property string $options
 * @property string $payments
 * @property string $services
 * @property int $subscription
 * @property int $drip_feed
 * @property int $ticket
 * @property int $custom_rates
 * @property int $referral_status
 */
class Users extends ActiveRecord
{
    public const PAYMENT_METHOD_ALLOW = 1;
    public const PAYMENT_METHOD_DISALLOW = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * {@inheritdoc}
     * @return \yii\db\Connection
     */
    public static function getDb()
    {
        return Yii::$app->panelDb;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login'], 'required'],
            [['referrer_id', 'hash_expiry_at', 'date', 'lastlogin', 'status', 'terms', 'timezone', 'subscription', 'ticket', 'custom_rates'], 'integer'],
            [['balance', 'spent'], 'number'],
            [['payments', 'services'], 'string'],
            [['login', 'apikey'], 'string', 'max' => 32],
            [['first_name', 'last_name', 'email', 'skype'], 'string', 'max' => 300],
            [['key', 'hash', 'auth_key'], 'string', 'max' => 64],
            [['hash_method', 'drip_feed'], 'string', 'max' => 1],
            [['lastip'], 'string', 'max' => 200],
            [['ref_key', 'lang'], 'string', 'max' => 10],
            [['options'], 'string', 'max' => 1000],
            [['referral_status'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'login' => Yii::t('app', 'Login'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'email' => Yii::t('app', 'Email'),
            'referrer_id' => Yii::t('app', 'Referrer ID'),
            'skype' => Yii::t('app', 'Skype'),
            'key' => Yii::t('app', 'Key'),
            'hash_method' => Yii::t('app', 'Hash Method'),
            'hash' => Yii::t('app', 'Hash'),
            'hash_expiry_at' => Yii::t('app', 'Hash Expiry At'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'balance' => Yii::t('app', 'Balance'),
            'spent' => Yii::t('app', 'Spent'),
            'date' => Yii::t('app', 'Date'),
            'lastlogin' => Yii::t('app', 'Lastlogin'),
            'lastip' => Yii::t('app', 'Lastip'),
            'apikey' => Yii::t('app', 'Apikey'),
            'ref_key' => Yii::t('app', 'Ref Key'),
            'status' => Yii::t('app', 'Status'),
            'terms' => Yii::t('app', 'Terms'),
            'timezone' => Yii::t('app', 'Timezone'),
            'lang' => Yii::t('app', 'Lang'),
            'options' => Yii::t('app', 'Options'),
            'payments' => Yii::t('app', 'Payments'),
            'services' => Yii::t('app', 'Services'),
            'subscription' => Yii::t('app', 'Subscription'),
            'drip_feed' => Yii::t('app', 'Drip Feed'),
            'ticket' => Yii::t('app', 'Ticket'),
            'custom_rates' => Yii::t('app', 'Custom Rates'),
            'referral_status' => Yii::t('app', 'Referral Status'),
        ];
    }

    /**
     * Get payments
     * @return array|null
     */
    public function getPayments(): ?array
    {
        return json_decode($this->payments, true);
    }

    /**
     * Set payments
     * @param array $payments
     */
    public function setPayments(array $payments)
    {
        $this->payments = json_encode($payments);
    }
}
