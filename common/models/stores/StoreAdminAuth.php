<?php
namespace common\models\stores;

use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

class StoreAdminAuth extends StoreAdmins implements IdentityInterface
{
    /**
     * Cached current auth key
     * @var
     */
    private $_auth_key;

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
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
        return $this->_auth_key ? $this->_auth_key : StoreAdminsHash::getHash($this->id);
    }

    /**
     * Set generated auth key
     */
    public function setAuthKey()
    {
        $this->_auth_key = $this->generateAuthKey();

        StoreAdminsHash::setHash($this->id, $this->_auth_key);
    }

    /**
     * Delete auth key
     */
    public function deleteAuthKey()
    {
        StoreAdminsHash::deleteByUser($this->id);
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
        $userAuthKey = $this->generateAuthKey();
        $cookieAuthKey = $authKey;

        return ($storedAuthKey === $cookieAuthKey) && ($storedAuthKey === $userAuthKey);
    }

    /**
     * Generates "remember me" authentication key
     * @return string
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $request = Yii::$app->getRequest();
        $salt = ArrayHelper::getValue(Yii::$app->params, 'salt', null);
        if (!$salt) {
            throw new Exception('\'auth_key\' is not defined in config -> params!');
        }

        $string2hash = $this->username . $this->password . $this->getPrimaryKey() . $request->getUserIP() . $request->getHeaders()->get('host') . $salt;

        $authKey = hash_hmac('sha256', $string2hash, $this->getSiteAuthKey());

        return $authKey;
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
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Validates password
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
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = hash_hmac('sha256', $password, $this->getSiteAuthKey());
    }
}
