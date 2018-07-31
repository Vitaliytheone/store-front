<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use common\models\stores\Stores;
use my\helpers\CustomerHelper;
use Yii;
use yii\db\ActiveRecord;
use DateTime;
use yii\db\Query;

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
 * @property integer $child_panels
 * @property integer $stores
 * @property integer $buy_domain
 * @property integer $date_create
 * @property integer $auth_date
 * @property string $auth_ip
 * @property integer $timezone
 * @property string $auth_token
 * @property string $unpaid_earnings
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
 * @property Project[] $projects
 * @property Project[] $actualProjects
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

    const REFERRAL_NOT_ACTIVE = 0;
    const REFERRAL_ACTIVE = 1;
    const REFERRAL_BLOCKED = 2;

    const REFERRAL_PAID = 1;
    const REFERRAL_NOT_PAID = 0;

    const STORES_ACTIVE = 1;
    const STORES_NOT_ACTIVE = 0;

    const BUY_DOMAIN_ACTIVE = 1;
    const BUY_DOMAIN_NOT_ACTIVE = 0;

    public $password_confirm;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.customers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password', 'password_confirm', 'first_name', 'last_name'], 'required', 'on' => self::SCENARIO_REGISTER],
            [['first_name', 'last_name'], 'required', 'on' => self::SCENARIO_SETTINGS],
            [['status', 'date_create', 'auth_date', 'timezone', 'referrer_id', 'referral_status', 'paid', 'referral_expired_at', 'child_panels', 'stores', 'buy_domain'], 'integer'],
            [['unpaid_earnings'], 'number'],
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
    public function getProjects()
    {
        return $this->hasMany(Project::class, ['cid' => 'id']);
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
    public function getReferrer()
    {
        return $this->hasOne(Customers::class, ['id' => 'referrer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferralVisits()
    {
        return $this->hasMany(ReferralVisits::class, ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferrals()
    {
        return $this->hasMany(Customers::class, ['referrer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnpaidReferrals()
    {
        return $this->hasMany(Customers::class, ['referrer_id' => 'id'])->onCondition([
            'paid' => static::REFERRAL_NOT_PAID
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaidReferrals()
    {
        return $this->hasMany(Customers::class, ['referrer_id' => 'id'])->onCondition([
            'paid' => static::REFERRAL_PAID
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferralEarnings()
    {
        return $this->hasMany(ReferralEarnings::class, ['customer_id' => 'id']);
    }

    /**
     * @return mixed
     */
    public function getTotalEarnings()
    {
        return $this->hasMany(ReferralEarnings::class, ['customer_id' => 'id'])->andOnCondition([
            'status' => ReferralEarnings::STATUS_COMPLETED
        ])->sum('earnings');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActualProjects()
    {
        return $this->hasMany(Project::class, ['cid' => 'id'])->onCondition('`project`.`act` IN(' . implode(",", [
            Project::STATUS_ACTIVE,
            Project::STATUS_FROZEN
        ]). ')');
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_SETTINGS => ['first_name','last_name'],
            self::SCENARIO_UPDATE => ['email', 'referrer_id'],
            self::SCENARIO_REGISTER => ['password_confirm', 'email', 'password','first_name','last_name'],
            self::SCENARIO_DEFAULT => ['password'],
        ];
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->findOne(['email' => $this->email]) !== null) {
                $this->addError('', 'Email already registered');
            }
        }
    }

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
            'child_panels' => Yii::t('app', 'Child Panels'),
            'stores' => Yii::t('app', 'Stores'),
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
     * Generate unique token
     */
    public function generateReferralLink()
    {
        $link = rand(10000, 99999);
        $result = static::findOne(['referral_link' => $link]);
        if($result !== null) {
            $this->generateReferralLink();
        } else {
            $this->referral_link = $link;
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
            case 'child':
                return $this->child_panels;
            break;

            case 'stores':
                return $this->stores;
            break;

            case 'domains':
                return $this->buy_domain;
            break;

            case 'referral':
                return static::REFERRAL_ACTIVE == $this->referral_status;
            break;

            case 'pay_referral':
                if (!$this->referrer_id) {
                    return false;
                }

                if (!empty($params['item'])) {
                    if (($params['item'] instanceof Project) && $params['item']->no_referral) {
                        return false;
                    }

                    if (($params['item'] instanceof Stores) && $params['item']->no_referral) {
                        return false;
                    }
                }
                $referrer = $this->referrer;

                return static::REFERRAL_ACTIVE == $referrer->referral_status && (time() <= $this->referral_expired_at);
            break;

            case 'enable_referral':
                return in_array($this->referral_status, [
                    static::REFERRAL_NOT_ACTIVE,
                    static::REFERRAL_BLOCKED
                ]);
            break;

            case 'disable_referral':
                return static::REFERRAL_ACTIVE == $this->referral_status;
            break;
        }

        return false;
    }

    /**
     * Activate customer child panels
     */
    public function activateChildPanels()
    {
        if ($this->child_panels) {
            return;
        }

        $this->child_panels = 1;
        $this->save(false);
    }

    /**
     * Activate customer referral status
     */
    public function activateReferral()
    {
        if ($this->paid) {
            return;
        }

        $this->paid = 1;
        $this->referral_status = static::REFERRAL_ACTIVE;
        $this->referral_expired_at = (new DateTime())->modify('+ ' . Yii::$app->params['referral_expiry'] . ' month')->getTimestamp();
        $this->save(false);
    }

    public function beforeSave($insert)
    {
        if (empty($this->referral_link)) {
            $this->generateReferralLink();
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * Return is customer have at least one project?
     * @return bool
     */
    public function hasPanels()
    {
        return (bool)CustomerHelper::getCountPanels($this->id, true);
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
     * Return is customer have prolonged panels
     * @return bool
     */
    public function hasProlongedPanels()
    {
        $panelsCount = (new Query())
            ->from(Project::tableName())
            ->andWhere(['cid' => $this->id])
            ->andWhere('`expired`-`date` > :period', [':period' => 45 * 24 * 60 * 60])
            ->count();

        return (bool)$panelsCount;
    }

    /**
     * Activate stores feature status
     */
    public function activateStores()
    {
        $this->stores = self::STORES_ACTIVE;

        return  $this->save(false);
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
