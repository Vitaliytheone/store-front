<?php

namespace common\models\panels;

use Yii;
use common\models\panels\queries\SuperAdminQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use common\components\traits\UnixTimeFormatTrait;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%super_admin}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $created_at
 * @property string $first_name
 * @property string $last_name
 * @property string $last_login
 * @property string $last_ip
 * @property string $auth_key
 * @property string $rules
 * @property integer $status
 *
 * @property SuperLog[] $superLogs
 */
class SuperAdmin extends ActiveRecord implements IdentityInterface
{
    const DEFAULT_ADMIN = 3;

    const STATUS_SUSPENDED = 0;
    const STATUS_ACTIVE = 1;

    const CAN_WORK_WITH_PANELS = 'panels';
    const CAN_WORK_WITH_ORDERS = 'orders';
    const CAN_WORK_WITH_DOMAINS = 'domains';
    const CAN_WORK_WITH_SSL = 'ssl';
    const CAN_WORK_WITH_CUSTOMERS = 'customers';
    const CAN_WORK_WITH_SETTINGS = 'settings';
    const CAN_WORK_WITH_INVOICES = 'invoices';
    const CAN_WORK_WITH_PAYMENTS = 'payments';
    const CAN_WORK_WITH_TICKETS = 'tickets';
    const CAN_WORK_WITH_PROVIDERS = 'providers';
    const CAN_WORK_WITH_REPORTS = 'reports';
    const CAN_WORK_WITH_STATUSES = 'statuses';
    const CAN_WORK_WITH_REFERRALS = 'referrals';
    const CAN_WORK_WITH_LOGS = 'logs';
    const CAN_WORK_WITH_STAFFS = 'staffs';
    const CAN_WORK_WITH_TOOLS = 'tools';

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.super_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'first_name', 'last_name', 'auth_key', 'status'], 'required'],
            [['created_at', 'status'], 'integer'],
            [['username', 'password', 'first_name', 'last_name', 'last_login', 'last_ip', 'auth_key'], 'string', 'max' => 250],
            [['rules'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'created_at' => Yii::t('app', 'Created At'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'last_login' => Yii::t('app', 'Last Login'),
            'last_ip' => Yii::t('app', 'Last Ip'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'rules' => Yii::t('app', 'Rules'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuperLogs()
    {
        return $this->hasMany(SuperLog::class, ['admin_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return SuperAdminQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SuperAdminQuery(get_called_class());
    }

    /**
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_SUSPENDED => Yii::t('app', 'super_admin.status.suspended'),
            static::STATUS_ACTIVE => Yii::t('app', 'super_admin.status.active')
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
     * Set rules
     * @param array $rules
     */
    public function setAccessRules($rules = [])
    {
        $this->rules = Json::encode(array_merge([
            static::CAN_WORK_WITH_PANELS => 0,
            static::CAN_WORK_WITH_ORDERS => 0,
            static::CAN_WORK_WITH_DOMAINS => 0,
            static::CAN_WORK_WITH_SSL => 0,
            static::CAN_WORK_WITH_CUSTOMERS => 0,
            static::CAN_WORK_WITH_INVOICES => 0,
            static::CAN_WORK_WITH_PAYMENTS => 0,
            static::CAN_WORK_WITH_TICKETS => 0,
            static::CAN_WORK_WITH_PROVIDERS => 0,
            static::CAN_WORK_WITH_REFERRALS => 0,
            static::CAN_WORK_WITH_REPORTS => 0,
            static::CAN_WORK_WITH_STATUSES => 0,
            static::CAN_WORK_WITH_LOGS => 0,
            static::CAN_WORK_WITH_STAFFS => 0,
            static::CAN_WORK_WITH_SETTINGS => 0,
            static::CAN_WORK_WITH_TOOLS => 0,
        ], $rules));
    }

    /**
     * Get rules
     * @param array $rules
     */
    public function getAccessRules()
    {
        $rulesList = [];

        $rules = !empty($this->rules) ? Json::decode($this->rules) : [];

        foreach ($rules as $access => $value) {
            if ($value) {
                $rulesList[] = $access;
            }
        }

        return $rulesList;
    }

    /**
     * Get default rules
     * @return array
     */
    public static function getDefaultRules()
    {
        return [
            static::CAN_WORK_WITH_PANELS => 1,
            static::CAN_WORK_WITH_ORDERS => 1,
            static::CAN_WORK_WITH_DOMAINS => 1,
            static::CAN_WORK_WITH_SSL => 1,
            static::CAN_WORK_WITH_CUSTOMERS => 1,
            static::CAN_WORK_WITH_INVOICES => 1,
            static::CAN_WORK_WITH_PAYMENTS => 1,
            static::CAN_WORK_WITH_TICKETS => 1,
            static::CAN_WORK_WITH_PROVIDERS => 1,
            static::CAN_WORK_WITH_REFERRALS => 1,
            static::CAN_WORK_WITH_REPORTS => 1,
            static::CAN_WORK_WITH_STATUSES => 1,
            static::CAN_WORK_WITH_LOGS => 1,
            static::CAN_WORK_WITH_STAFFS => 1,
            static::CAN_WORK_WITH_SETTINGS => 1,
            static::CAN_WORK_WITH_TOOLS => 1,
        ];
    }

    /**
     * Get rules labels
     * @return array
     */
    public static function getRulesLabels()
    {
        return [
            static::CAN_WORK_WITH_PANELS => Yii::t('app', 'super_admin.can_work_with.panels'),
            static::CAN_WORK_WITH_ORDERS => Yii::t('app', 'super_admin.can_work_with.orders'),
            static::CAN_WORK_WITH_DOMAINS => Yii::t('app', 'super_admin.can_work_with.domains'),
            static::CAN_WORK_WITH_SSL => Yii::t('app', 'super_admin.can_work_with.ssl'),
            static::CAN_WORK_WITH_CUSTOMERS => Yii::t('app', 'super_admin.can_work_with.customers'),
            static::CAN_WORK_WITH_INVOICES => Yii::t('app', 'super_admin.can_work_with.invoices'),
            static::CAN_WORK_WITH_PAYMENTS => Yii::t('app', 'super_admin.can_work_with.payments'),
            static::CAN_WORK_WITH_TICKETS => Yii::t('app', 'super_admin.can_work_with.tickets'),
            static::CAN_WORK_WITH_PROVIDERS => Yii::t('app', 'super_admin.can_work_with.providers'),
            static::CAN_WORK_WITH_REFERRALS => Yii::t('app', 'super_admin.can_work_with.referrals'),
            static::CAN_WORK_WITH_REPORTS => Yii::t('app', 'super_admin.can_work_with.reports'),
            static::CAN_WORK_WITH_STATUSES => Yii::t('app', 'super_admin.can_work_with.statuses'),
            static::CAN_WORK_WITH_LOGS => Yii::t('app', 'super_admin.can_work_with.logs'),
            static::CAN_WORK_WITH_STAFFS => Yii::t('app', 'super_admin.can_work_with.staffs'),
            static::CAN_WORK_WITH_SETTINGS => Yii::t('app', 'super_admin.can_work_with.settings'),
            static::CAN_WORK_WITH_TOOLS => Yii::t('app', 'super_admin.can_work_with.tools'),
        ];
    }

    /**
     * Set password
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = hash_hmac('sha256', $password, Yii::$app->params['auth_key']);
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ]
        ];
    }

    /**
     * Generate auth key value
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()->where('id = :id', [
            ':id' => $id
        ])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === hash_hmac('sha256', $password, Yii::$app->params['auth_key']);
    }

    /**
     * Get full customer name
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}