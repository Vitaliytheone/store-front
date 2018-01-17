<?php

namespace common\models\stores;

use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\stores\queries\StoreAdminsQuery;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%store_admins}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property string $username
 * @property string $password
 * @property string $auth_hash
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
class StoreAdmins extends ActiveRecord implements IdentityInterface
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

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
            [['password', 'auth_hash'], 'string', 'max' => 64],
            [['rules'], 'string', 'max' => 1000],
            ['status', 'in', 'range' => [self::STATUS_DISABLED, self::STATUS_ENABLED]],
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
            'auth_hash' => Yii::t('app', 'Auth Hash'),
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
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ENABLED]);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not allowed');
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     *
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     * @throws Exception
     */
    public function getAuthKey()
    {
        return $this->auth_hash;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        $storedAuthKey = $this->getAuthKey();
        $userAuthKey = $this->generateAuthKey(false);
        $cookieAuthKey = $authKey;

        return ($storedAuthKey === $cookieAuthKey) && ($storedAuthKey === $userAuthKey);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @param bool $set Update model corresponding field if true
     * @return string
     * @throws Exception
     */
    public function generateAuthKey($set = true)
    {
        $request = Yii::$app->getRequest();
        $salt = ArrayHelper::getValue(Yii::$app->params, 'salt', null);
        if (!$salt) {
            throw new Exception('\'auth_key\' is not defined in config -> params!');
        }

        $string2hash = $this->username . $this->password . $this->getPrimaryKey() . $request->getUserIP() . $request->getHeaders()->get('host') . $salt;

        $auth_key = hash_hmac('sha256', $string2hash, $this->getSiteAuthKey());

        if ($set) {
            $this->auth_hash = $auth_key;
        }

        return $auth_key;
    }

    /**
     * Return site auth key from config params
     * @return mixed
     * @throws Exception
     */
    public function getSiteAuthKey()
    {
        $siteAuthKey = ArrayHelper::getValue(Yii::$app->params, 'auth_key', null);

        if (!$siteAuthKey) {
            throw new Exception('\'auth_key\' is not defined in config -> params!');
        }

        return $siteAuthKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ENABLED]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $passwordHash = hash_hmac('sha256', $password, $this->getSiteAuthKey());

        return $this->password === $passwordHash;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = hash_hmac('sha256', $password, $this->getSiteAuthKey());
    }

    /**
     * Return current admin allowed controllers list
     * [
     *    'admin/orders,
     *    'admin/settings',
     * ]
     * @return mixed
     */
    public function getRules()
    {
        $rules = json_decode($this->rules);

        if (!is_array($rules)) {
            return [];
        }

        array_walk($rules, function (&$rule){
            $rule = 'admin/' . $rule;
        });

        return $rules;
    }

    /**
     * Event handler for `on afterLogin` event
     * @param $event
     */
    static function updateLoginData($event)
    {
        /** @var StoreAdmins $user */
        $user = $event->identity;

        $user->ip = Yii::$app->getRequest()->getUserIP();
        $user->last_login = time();
        $user->generateAuthKey();

        $user->save();
    }

    /**
     * Event handler for `on afterLogout` event
     * @param $event
     */
    static function updateLogoutData($event)
    {
        /** @var StoreAdmins $user */
        $user = $event->identity;
        $user->auth_hash = null;

        $user->save();
    }

}
