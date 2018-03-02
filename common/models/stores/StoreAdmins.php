<?php
namespace common\models\stores;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\stores\queries\StoreAdminsQuery;

/**
 * This is the model class for table "{{%store_admins}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property string $username
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property integer $status
 * @property string $ip
 * @property integer $last_login
 * @property string $rules
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Stores $store
 */
class StoreAdmins extends ActiveRecord
{
    const STATUS_ACTIVE     = 1;
    const STATUS_SUSPENDED  = 2;

    const SUPER_USER_MODE_OFF = 0;
    const SUPER_USER_MODE_ON = 1;

    /**
     * Default allowed controller
     * Admin will be redirect to this controller
     * if other controllers are not allowed
     */
    const DEFAULT_CONTROLLER = 'account';

    /**
     * Prefix of the admin module
     * will be added to allowed controller rules like:
     * MODULE_PREFIX/CONTROLLER
     */
    const MODULE_PREFIX = 'admin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%store_admins}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'status', 'last_login', 'created_at', 'updated_at'], 'integer'],
            [['username', 'first_name', 'last_name', 'ip'], 'string', 'max' => 255],
            [['password',], 'string', 'max' => 64],
            [['rules'], 'string', 'max' => 1000],
            ['status', 'in', 'range' => [self::STATUS_SUSPENDED, self::STATUS_ACTIVE]],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'store_id' => Yii::t('app', 'Store ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'status' => Yii::t('app', 'Status'),
            'ip' => Yii::t('app', 'Ip'),
            'last_login' => Yii::t('app', 'Last Login'),
            'rules' => Yii::t('app', 'Rules'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['id' => 'store_id']);
    }

    /**
     * @inheritdoc
     * @return StoreAdminsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoreAdminsQuery(get_called_class());
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * Return if admin active
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Return current admin allowed rules list
     * Default allowed controller will be present
     * if $includeDefault is true
     *
     * [
     *      'orders => 1,
     *      'settings' => 0,
     *      ...
     *      default_controller => 1
     * ]
     *
     * @param $includeDefault bool
     * @return array
     */
    public function getRules($includeDefault = true)
    {
        $rules = json_decode($this->rules, true);

        if (!is_array($rules)) {
            $rules = [];
        }

        if ($includeDefault) {
            // Add default allowed controller to rules array
            $rules[self::DEFAULT_CONTROLLER] = 1;
        }

        return $rules;
    }

    /**
     * Return current admin allowed controllers names list
     * [
     *  'orders',
     *  'settings'..
     * ]
     */
    public function getAllowedControllersNames()
    {
        // Return only allowed rules
        $rules = array_filter($this->getRules(), function($rule){
            return !!$rule;
        });

        $controllers = array_keys($rules);

        return $controllers;
    }

    /**
     * Return current admin allowed controllers list
     *
     * [
     *      'admin/orders',
     *      'admin/settings',
     *      ...
     * ]
     * @return array
     */
    public function getAllowedControllers()
    {
        $controllers =$this->getAllowedControllersNames();

        array_walk($controllers, function (&$controller){
            $controller = self::MODULE_PREFIX . '/' . $controller;
        });

        return $controllers;
    }

}
