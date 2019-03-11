<?php
namespace common\models\sommerces;

use common\models\sommerces\SuperAdminToken;
use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * Class StoreAdminAuth
 * @package common\models\sommerces
 *
 * @property StoreAdminsHash $hash
 */
class StoreAdminAuth extends StoreAdmins implements IdentityInterface
{
    /** Auth cookie lifetime */
    const COOKIE_LIFETIME = 365 * 24 * 60 * 60;

    const SESSION_KEY_ADMIN_HASH = 'super_admin_hash';

    /**
     * Cached copy of StoreAdminHash model
     * @var  null|StoreAdminsHash
     */
    private static $_hash;

    /**
     * Cached copy of StoreAdminHash model
     * @var  null|StoreAdminAuth
     */
    private static $_identity;

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
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        if (static::$_identity instanceof StoreAdminAuth) {
            return static::$_identity;
        }

        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        $hash = static::getHash($id);

        if (!$hash || $hash->admin_id != $id) {
            return null;
        }

        if ($hash->super_user == StoreAdminsHash::MODE_SUPERADMIN_ON) {

            static::$_identity = static::_getSuperadminIdentity($id);

        } else if ($store) {

            static::$_identity = static::findOne([
                'id' => $id,
                'store_id' => $store->id,
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
        return static::getHash($this->getId())->hash;
    }

    /**
     * Validates the given auth key.
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
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
     * Return StoreAdmin hash object
     * @param $adminId int|string Current admin/superadmin ID
     * @return StoreAdminsHash|null
     */
    public static function getHash($adminId)
    {
        if (static::$_hash instanceof StoreAdminsHash) {
            return static::$_hash;
        }

        $hash = static::generateAuthKey($adminId);

        static::$_hash = StoreAdminsHash::findOne(['hash' => $hash]);

        return static::$_hash;
    }

    /**
     * Finds user by username
     * @param string $username
     * @return static|null
     */
    public static function findByUsername(string $username)
    {
        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        return static::findOne(['store_id' => $store->id, 'username' => $username]);
    }

    /**
     * Find superadmin by superadmin token
     * @param string $token
     * @return null|static
     */
    public static function findByToken(string $token)
    {
        $store = Yii::$app->store->getInstance();

        $superAdminToken = SuperAdminToken::find()
            ->andFilterWhere([
                'token' => $token,
                'item_id' => $store->id,
                'item' => SuperAdminToken::ITEM_SOMMERCE,
            ])
            ->andFilterWhere(['>', 'expiry_at', time()])
            ->one();

        if (!$superAdminToken) {
            return null;
        }

        $identity = static::_getSuperadminIdentity($superAdminToken->super_admin_id);

        $superAdminToken->delete();

        return $identity;
    }

    /**
     * Return SuperAdmin identity with virtual id
     * @param $superadminId
     * @return StoreAdminAuth|null
     */
    private static function _getSuperadminIdentity($superadminId)
    {
        $identity = static::findOne([
            'store_id' => self::SUPERADMIN_STORE_ID,
        ]);

        $identity->id = $superadminId;

        static::$_identity = $identity;

        return static::$_identity;
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $passwordHash = static::hashPassword($password);

        return $this->password === $passwordHash;
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = static::hashPassword($password);
    }

    /**
     * @param $password
     * @return string
     */
    public static function hashPassword($password)
    {
        return hash_hmac('sha256', $password, static::getSalt());
    }

    /**
     * Return is user is logged in as superadmin
     * @return bool
     */
    public function isSuperAdmin()
    {
        return (bool)static::getHash($this->getId())->super_user;
    }

    /**
     * Admin logout admin
     */
    public function logout()
    {
        StoreAdminsHash::deleteByUser($this->id);
    }
}
