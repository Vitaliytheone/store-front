<?php

namespace common\models\gateways;

use common\models\panels\SuperAdminToken;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\gateways\queries\AdminsQuery;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use Exception;

/**
 * This is the model class for table "{{%admins}}".
 *
 * @property int $id
 * @property int $site_id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property int $status 1 - active; 2 - suspended
 * @property string $ip
 * @property int $last_login
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Sites $site
 */
class Admins extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE     = 1;
    const STATUS_SUSPENDED  = 2;

    const SUPERADMIN_SITE_ID = '-1';

    /** Auth cookie lifetime */
    const COOKIE_LIFETIME = 365 * 24 * 60 * 60;

    const SESSION_KEY_ADMIN_HASH = 'super_admin_hash';

    /**
     * Cached copy of model
     * @var  null|static
     */
    private static $_identity;

    private static $_hash;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_GATEWAYS . '.admins';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'username', 'password'], 'required'],
            [['site_id', 'last_login', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'auth_key', 'ip'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 1],
            [['site_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sites::class, 'targetAttribute' => ['site_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'site_id' => Yii::t('app', 'Site ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'status' => Yii::t('app', 'Status'),
            'ip' => Yii::t('app', 'Ip'),
            'last_login' => Yii::t('app', 'Last Login'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Sites::class, ['id' => 'site_id']);
    }

    /**
     * @inheritdoc
     * @return AdminsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AdminsQuery(get_called_class());
    }

    /**
     * Return if admin active
     * @return bool
     */
    public function isActive()
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     *
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Finds an identity by the given ID.
     * @param int|string $id $id the ID to be looked for
     * @return Admins|IdentityInterface|null the identity object that matches the given ID.
     * @throws Exception
     */
    public static function findIdentity($id)
    {
        if (static::$_identity instanceof Admins) {
            return static::$_identity;
        }

        /** @var Sites $site */
        $site = Yii::$app->gateway->getInstance();

        $hash = static::getHash($id);

        if (!$hash || $hash->admin_id != $id) {
            return null;
        }

        if ($hash->super_user == AdminsHash::MODE_SUPERADMIN_ON) {

            static::$_identity = static::getSuperadminIdentity($id);

        } else if ($site) {
            static::$_identity = static::findOne([
                'id' => $id,
                'site_id' => $site->id,
                'status' => self::STATUS_ACTIVE
            ]);
        }

        return static::$_identity;
    }


    /**
     * Finds an identity by the given token.
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
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @throws Exception
     */
    public function validateAuthKey($authKey)
    {
        $storedAuthKey = $this->getAuthKey();
        $userAuthKey = static::generateAuthKey($this->getId());
        $cookieAuthKey = $authKey;

        return ($storedAuthKey === $cookieAuthKey) && ($storedAuthKey === $userAuthKey);
    }

    /**
     * Generates "remember me" authentication key
     * @param $adminId string|integer Current admin/superadmin id
     * @return string
     * @throws Exception
     */
    public static function generateAuthKey($adminId)
    {
        $request = Yii::$app->getRequest();

        $string2hash =  $adminId . $request->getUserIP() . $request->getHeaders()->get('host');

        $authKey = hash_hmac('sha256', $string2hash, static::getSalt());

        return $authKey;
    }

    /**
     * Get hash
     * @param $adminId
     * @return mixed
     * @throws Exception
     */
    public static function getHash($adminId)
    {
        if (static::$_hash instanceof AdminsHash) {
            return static::$_hash;
        }

        $hash = static::generateAuthKey($adminId);

        static::$_hash = AdminsHash::findOne(['hash' => $hash]);

        return static::$_hash;
    }

    /**
     * Return site auth key from config params
     * @return mixed
     * @throws Exception
     */
    public static function getSalt()
    {
        $siteAuthKey = ArrayHelper::getValue(Yii::$app->params, 'auth_key', null);

        if (!$siteAuthKey) {
            throw new Exception('\'auth_key\' is not defined in config -> params!');
        }

        return $siteAuthKey;
    }

    /**
     * Finds user by username
     * @param string $username
     * @return static|null
     */
    public static function findByUsername(string $username)
    {
        /** @var Sites $site */
        $site = Yii::$app->gateway->getInstance();

        return static::findOne(['site_id' => $site->id, 'username' => $username]);
    }

    /**
     * Validates password
     * @param string $password string $password password to validate
     * @return bool if password provided is valid for current user
     * @throws Exception
     */
    public function validatePassword($password)
    {
        $passwordHash = static::hashPassword($password);

        return $this->password === $passwordHash;
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password = static::hashPassword($password);
    }

    /**
     * @param $password
     * @return string
     * @throws Exception
     */
    public static function hashPassword($password)
    {
        return hash_hmac('sha256', $password, static::getSalt());
    }

    /**
     * @param string $token
     * @return Admins|null
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function findByToken(string $token)
    {
        $gateway = Yii::$app->gateway->getInstance();

        $superAdminToken = SuperAdminToken::find()
            ->andFilterWhere([
                'token' => $token,
                'item_id' => $gateway->id,
                'item' => SuperAdminToken::ITEM_GATEWAY,
            ])
            ->andFilterWhere(['>', 'expiry_at', time()])
            ->one();

        if (!$superAdminToken) {
            return null;
        }

        $identity = static::getSuperadminIdentity($superAdminToken->super_admin_id);

        $superAdminToken->delete();

        return $identity;
    }

    /**
     * @param $superadminId
     * @return Admins|null
     */
    private static function getSuperadminIdentity($superadminId)
    {
        $identity = static::findOne([
            'site_id' => self::SUPERADMIN_SITE_ID,
        ]);

        $identity->id = $superadminId;

        static::$_identity = $identity;

        return static::$_identity;
    }

    /**
     * Admin logout admin
     */
    public function logout()
    {
        AdminsHash::deleteByUser($this->id);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
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
        ]);
    }
}