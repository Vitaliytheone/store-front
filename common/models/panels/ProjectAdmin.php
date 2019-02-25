<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%project_admin}}".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $login
 * @property string $passwd
 * @property string $hash
 * @property integer $last_login
 * @property string $last_ip
 * @property integer $status
 * @property integer $date
 * @property integer $update_at
 * @property string $rules
 * @property integer $rules_users
 * @property integer $rules_orders
 * @property integer $rules_services
 * @property integer $rules_payments
 * @property integer $rules_stats
 * @property integer $rules_tickets
 * @property integer $rules_content
 * @property integer $rules_settings
 * @property integer $rules_admins
 * @property integer $rules_dripfeed
 * @property integer $rules_themes
 * @property integer $rules_subscriptions
 * @property integer $rules_tasks
 * @property integer $rules_pages
 * @property integer $rules_providers
 *
 * @property Project $project
 */
class ProjectAdmin extends ActiveRecord
{
    const STATUS_ACTIVE = 0;
    const STATUS_SUSPENDED = 1;

    static $defaultRules = [
        'users' => 1,
        'orders' => 1,
        'subscription' => 1,
        'tasks' => 1,
        'dripfeed' => 1,
        'services' => 1,
        'payments' => 1,
        'tickets' => 1,
        'reports' => 1,
        'affiliates' => 1,
        'tools' => 1,
        'providers' => 0,
        'settings_general' => 1,
        'settings_providers' => 1,
        'settings_payments' => 1,
        'settings_bonuses' => 1,
        'settings_pages' => 1,
        'settings_menu' => 1,
        'settings_preferences' => 1,
        'appearance_themes' => 1,
        'appearance_languages' => 1,
    ];

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.project_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'login', 'passwd'], 'required'],
            [['pid', 'last_login', 'status', 'date', 'update_at', 'rules_users', 'rules_orders', 'rules_services', 'rules_payments', 'rules_stats', 'rules_tickets', 'rules_content', 'rules_settings', 'rules_admins', 'rules_dripfeed', 'rules_themes', 'rules_subscriptions', 'rules_tasks', 'rules_pages', 'rules_providers'], 'integer'],
            [['login', 'passwd', 'last_ip'], 'string', 'max' => 300],
            [['hash'], 'string'],
            [['rules'], 'safe'],
            [['rules'], 'default', 'value' => json_encode(static::$defaultRules)],
            [['status'], 'default', 'value' => static::STATUS_ACTIVE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pid' => Yii::t('app', 'Pid'),
            'login' => Yii::t('app', 'Login'),
            'passwd' => Yii::t('app', 'Passwd'),
            'hash' => Yii::t('app', 'Hash'),
            'last_login' => Yii::t('app', 'Last Login'),
            'last_ip' => Yii::t('app', 'Last Ip'),
            'status' => Yii::t('app', 'Status'),
            'date' => Yii::t('app', 'Date'),
            'update_at' => Yii::t('app', 'Update At'),
            'rules' => Yii::t('app', 'Rules'),
            'rules_users' => Yii::t('app', 'Rules Users'),
            'rules_orders' => Yii::t('app', 'Rules Orders'),
            'rules_services' => Yii::t('app', 'Rules Services'),
            'rules_payments' => Yii::t('app', 'Rules Payments'),
            'rules_stats' => Yii::t('app', 'Rules Stats'),
            'rules_tickets' => Yii::t('app', 'Rules Tickets'),
            'rules_content' => Yii::t('app', 'Rules Content'),
            'rules_settings' => Yii::t('app', 'Rules Settings'),
            'rules_admins' => Yii::t('app', 'Rules Admins'),
            'rules_dripfeed' => Yii::t('app', 'Rules Dripfeed'),
            'rules_themes' => Yii::t('app', 'Rules Themes'),
            'rules_subscriptions' => Yii::t('app', 'Rules Subscriptions'),
            'rules_tasks' => Yii::t('app', 'Rules Tasks'),
            'rules_pages' => Yii::t('app', 'Rules Pages'),
            'rules_providers' => Yii::t('app', 'Rules Providers'),
        ];
    }

    /**
     * Get rules with labels
     * @return array
     */
    public static function getRulesLabels()
    {
        return [
            'users' => Yii::t('app', 'project_admin.rules_users'),
            'orders' => Yii::t('app', 'project_admin.rules_orders'),
            'subscription' => Yii::t('app', 'project_admin.rules_subscriptions'),
            'tasks' => Yii::t('app', 'project_admin.rules_tasks'),
            'dripfeed' => Yii::t('app', 'project_admin.rules_dripfeed'),
            'services' => Yii::t('app', 'project_admin.rules_services'),
            'payments' => Yii::t('app', 'project_admin.rules_payments'),
            'tickets' => Yii::t('app', 'project_admin.rules_tickets'),
            'reports' => Yii::t('app', 'project_admin.rules_stats'),
            'affiliates' => Yii::t('app', 'project_admin.rules_affiliate'),
            'tools' => Yii::t('app', 'project_admin.rules_tools'),
            'providers' => Yii::t('app', 'project_admin.rules_providers'),
            'settings' => Yii::t('app', 'project_admin.rules_settings'),
            'settings_general' => Yii::t('app', 'project_admin.rules_settings_general'),
            'settings_providers' => Yii::t('app', 'project_admin.rules_settings_providers'),
            'settings_payments' => Yii::t('app', 'project_admin.rules_settings_payments'),
            'settings_bonuses' => Yii::t('app', 'project_admin.rules_settings_bonuses'),
            'settings_pages' => Yii::t('app', 'project_admin.rules_settings_pages'),
            'settings_menu' => Yii::t('app', 'project_admin.rules_settings_menu'),
            'settings_preferences' => Yii::t('app', 'project_admin.rules_settings_preferences'),
            'appearance_themes' => Yii::t('app', 'project_admin.rules_settings_themes'),
            'appearance_languages' => Yii::t('app', 'project_admin.rules_settings_languages'),
            'appearance' => Yii::t('app', 'project_admin.rules_appearance'),
        ];
    }

    /**
     * Set rules
     * @param array $rules
     */
    public function setRules(array $rules)
    {
        $defaultRules = array_fill_keys(array_keys(static::$defaultRules), 0);
        $rules = ArrayHelper::merge($defaultRules, $rules);
        $rules = array_intersect_key($rules, $defaultRules);
        $this->rules = json_encode($rules);
    }

    /**
     * Get rules
     * @return array
     */
    public function getRules()
    {
        $rules = (array)json_decode($this->rules, true);
        if (empty($rules)) {
            return $rules;
        }

        $this->setRules($rules);

        return json_decode($this->rules, true);
    }

    /**
     * get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_ACTIVE => Yii::t('app', 'project_admin.status.active'),
            static::STATUS_SUSPENDED => Yii::t('app', 'project_admin.status.suspended')
        ];
    }

    /**
     * Get status name
     * @return mixed
     */
    public function getStatusName()
    {
        $statuses = static::getStatuses();
        return ArrayHelper::getValue($statuses, $this->status, 'Active');
    }

    public static function hashPassword($password)
    {
        return hash_hmac('sha256', $password, Yii::$app->params['admin_auth_key']);
    }

    /**
     * Set password
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwd = static::hashPassword($password);
    }

    /**
     * Is full access
     * @return bool
     */
    public function isFullAccess()
    {
        $rules = $this->getRules();

        // 25.02.2019 Александр http://prntscr.com/mpi176
        //надо пока закоментировать это, сделайте это срочно
        unset($rules['tools']);
        if (empty($rules)) {
            return false;
        }

        if (0 != $rules['providers']) {
            return false;
        }
        unset($rules['providers']);

        return !in_array(0, $rules);
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'pid']);
    }
}
