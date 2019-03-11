<?php

namespace control_panel\models;

use common\models\sommerces\MyCustomersHash;
use yii\web\IdentityInterface;
use Yii;
use common\models\sommerces\Customers;

/**
 * Class Auth
 * @package common\models\sommerces
 */
class Auth extends Customers implements IdentityInterface
{
    public static $_identity;

    public $isSuperadminAuth = false;

    /**
     * Finds an identity by the given ID.
     *Customers
     * @param string|integer $id the ID to be looked for
     * @return Customers|null.
     */
    public static function findIdentity($id)
    {
        if (null == static::$_identity) {
            static::$_identity = static::findOne($id);
        }
        return static::$_identity;
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return Customers|null.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }


    public static function findByUsername($email, $type = null)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return MyCustomersHash::getHash($this);
    }

    /**
     * @return string current user auth key
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */

    public function validateAuthKey($authKey)
    {
        return MyCustomersHash::validateHash($this, $authKey);
    }

    /**
     * Set auth key
     * @param bool $remember
     * @param bool $super
     */
    public function setAuthKey($remember = false, $super = false)
    {
        MyCustomersHash::setHash($this, $remember, $super);
    }

    /**
     * Clear auth key
     * @param string $hash
     */
    public function clearAuthKey($hash)
    {
        MyCustomersHash::remove($hash);
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
     * Validates password
     *
     * @param string $password password to validate
     * @param bool $hash
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password, $hash = false)
    {
        if ($hash) {
            return $this->getPassword() === $password;
        }
        return $this->getPassword() === hash_hmac('sha256', $password, Yii::$app->params['auth_key']);
    }

    /**
     * Generate unique auth key
     * @return string
     */
    public function generateAuthKey()
    {
        return hash_hmac('sha256', substr(md5(rand() . microtime() . rand()), 0, 32), Yii::$app->params['auth_key']);
    }
}