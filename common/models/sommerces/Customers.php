<?php

namespace common\models\sommerces;

use common\components\traits\UnixTimeFormatTrait;
use my\helpers\CustomerHelper;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%customers}}".
 *
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $access_token
 * @property string $token
 * @property integer $status
 * @property integer $buy_domain
 * @property integer $date_create
 * @property integer $auth_date
 * @property string $auth_ip
 * @property integer $timezone
 * @property string $auth_token
 * @property integer $unpaid_earnings
 * @property integer $referrer_id
 * @property integer $referral_status
 * @property integer $paid
 * @property string $referral_link
 * @property integer $referral_expired_at
 *
 * @property Invoices[] $invoices
 * @property Payments[] $payments
 * @property Payments[] $successPayments
 * @property Domains[] $domains
 * @property SslCert[] $sslCerts
 * @property ReferralVisits[] $referralVisits
 * @property Customers[] $unpaidReferrals
 * @property Customers[] $paidReferrals
 * @property ReferralEarnings[] $referralEarnings
 * @property mixed $totalEarnings
 * @property Customers $referrer
 * @property Customers[] $referrals
 */
class Customers extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_SUSPENDED = 0;

    const SCENARIO_SETTINGS = 'settings';
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DEFAULT = 'default';

    const BUY_DOMAIN_ACTIVE = 1;
    const BUY_DOMAIN_NOT_ACTIVE = 0;

    public $password_confirm;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_SOMMERCES . '.customers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password', 'password_confirm', 'first_name', 'last_name'], 'required', 'on' => self::SCENARIO_REGISTER],
            [['first_name', 'last_name'], 'required', 'on' => self::SCENARIO_SETTINGS],
            [['unpaid_earnings', 'status', 'date_create', 'auth_date', 'timezone', 'referrer_id', 'referral_status', 'paid', 'referral_expired_at', 'buy_domain'], 'integer'],
            [['referral_link'], 'string', 'max' => 5],
            [['first_name'], 'string', 'max' => 300],
            [['last_name'], 'string', 'max' => 300],
            [['email'], 'string', 'max' => 300],
            [['token'], 'string', 'max' => 32],
            [['auth_token'], 'string', 'max' => 32],
            [['password'], 'string', 'max' => 64],
            [['auth_ip'], 'string', 'max' => 100],
            ['email', 'validateEmail', 'on' => self::SCENARIO_REGISTER],
            ['email', 'email', 'on' => self::SCENARIO_REGISTER],
            ['password', 'passwordValidator', 'on' => self::SCENARIO_REGISTER],
            ['email', 'trim', 'on' => self::SCENARIO_UPDATE],
            ['email', 'email', 'on' => self::SCENARIO_UPDATE],
            ['email', 'unique', 'on' => self::SCENARIO_UPDATE],
            ['email', 'required', 'on' => self::SCENARIO_UPDATE],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoices::class, ['cid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payments::class, ['iid' => 'id'])->via('invoices');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuccessPayments()
    {
        return $this->hasMany(Payments::class, ['iid' => 'id'])
            ->onCondition([
                'payments.status' => Payments::STATUS_COMPLETED
            ])
            ->via('invoices');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomains()
    {
        return $this->hasMany(Domains::class, ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSslCerts()
    {
        return $this->hasMany(SslCert::class, ['cid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStores()
    {
        return $this->hasMany(Stores::class, ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActualProjects()
    {
        return $this->hasMany(Stores::class, ['customer_id' => 'id'])->onCondition('`stores`.`status` IN(' . implode(",", [
            Stores::STATUS_ACTIVE,
            Stores::STATUS_FROZEN
        ]). ')');
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_SETTINGS => ['first_name','last_name'],
            self::SCENARIO_UPDATE => ['email', 'referrer_id'],
            self::SCENARIO_REGISTER => ['password_confirm', 'email', 'password','first_name','last_name'],
            self::SCENARIO_DEFAULT => ['password'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->findOne(['email' => $this->email]) !== null) {
                $this->addError('', 'Email already registered');
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function passwordValidator($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->password !== $this->password_confirm) {
                $this->addError('', 'Passwords not match');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password_confirm' => 'Confirm password',
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'access_token' => Yii::t('app', 'Access Token'),
            'token' => Yii::t('app', 'Token'),
            'status' => Yii::t('app', 'Status'),
            'buy_domain' => Yii::t('app', 'Domains'),
            'date_create' => Yii::t('app', 'Date Create'),
            'auth_date' => Yii::t('app', 'Auth Date'),
            'auth_ip' => Yii::t('app', 'Auth Ip'),
            'timezone' => Yii::t('app', 'Timezone'),
            'auth_token' => Yii::t('app', 'Auth Token'),
            'unpaid_earnings' => Yii::t('app', 'Unpaid Earnings'),
            'referrer_id' => Yii::t('app', 'Referrer ID'),
            'referral_status' => Yii::t('app', 'Referral Status'),
            'paid' => Yii::t('app', 'Paid'),
            'referral_link' => Yii::t('app', 'Referral Link'),
            'referral_expired_at' => Yii::t('app', 'Referral Expired At'),
        ];
    }

    /**
     * Generate unique token
     */
    public function generateToken()
    {
        $token = substr(md5(microtime()), 0, 32);
        $result = static::findOne(['token' => $token]);
        if($result !== null) {
            $this->generateToken();
        } else {
            $this->token = $token;
        }
    }

    /**
     * Set password
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = hash_hmac('sha256', $password, Yii::$app->params['auth_key']);
    }

    /**
     * Check password
     * @param string $password
     * @return bool
     */
    public function checkPassword($password)
    {
        return $this->password == hash_hmac('sha256', $password, Yii::$app->params['auth_key']);
    }

    /**
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_ACTIVE => Yii::t('app', 'customers.status.active'),
            static::STATUS_SUSPENDED => Yii::t('app', 'customers.status.suspended')
        ];
    }

    /**
     * Get status name
     * @return string
     */
    public function getStatusName()
    {
        return static::getStatuses()[$this->status];
    }

    /**
     * Change status
     * @param int $status
     * @return bool
     */
    public function changeStatus($status)
    {
        switch ($status) {
            case static::STATUS_ACTIVE:
                if (static::STATUS_SUSPENDED == $this->status) {
                    $this->status = static::STATUS_ACTIVE;
                }
            break;

            case static::STATUS_SUSPENDED:
                if (static::STATUS_ACTIVE == $this->status) {
                    $this->status = static::STATUS_SUSPENDED;
                }
            break;
        }

        return $this->save(false);
    }

    /**
     * Generate auth token
     */
    public function generateAuthToken()
    {
        $this->auth_token = hash_hmac('sha256', $this->email . '_' . $this->password . '_' . Yii::$app->getRequest()->getUserIP(), Yii::$app->params['access_key']);
    }

    /**
     * Get full customer name
     * @return string
     */
    public function getFullName()
    {
        return trim(htmlspecialchars($this->first_name . ' ' . $this->last_name));
    }

    /**
     * Check access to some actions
     * @param string $code
     * @param array $params
     * @return bool
     */
    public function can($code, $params = [])
    {
        switch ($code) {
            case 'stores':
                return true;

            case 'domains':
                if (!Domains::find()->where(['customer_id' => $this->id])->exists()) {
                    return false;
                }

                return $this->buy_domain;
            break;

            case 'ssl':
                $sslCerts = SslCert::find()
                    ->leftJoin('ssl_cert_item', 'ssl_cert.item_id = ssl_cert_item.id')
                    ->where([
                        'ssl_cert.cid' => $this->id,
                        'ssl_cert.status' => SslCert::STATUS_ACTIVE,
                        'ssl_cert_item.provider' => SslCertItem::PROVIDER_GOGETSSL
                    ])
                    ->exists();

                if ($sslCerts) {
                    return true;
                }

                $stores = Stores::find()
                    ->where([
                        'customer_id' => $this->id,
                        'status' => Stores::STATUS_ACTIVE,
                        'ssl' => Stores::SSL_MODE_OFF,
                        'dns_status' => null,
                    ]);

                if ($stores->exists()) {
                    return true;
                }
                return false;
            break;
        }

        return false;
    }

    /**
     * Return is customer have at least one store?
     * @return bool
     */
    public function hasStores()
    {
        return (bool)CustomerHelper::getCountStores($this->id, true);
    }

    /**
     * Activate stores feature status
     */
    public function activateDomains()
    {
        $this->buy_domain = self::BUY_DOMAIN_ACTIVE;

        return $this->save(false);
    }
}
