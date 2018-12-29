<?php
namespace admin\models\forms;

use common\models\gateways\Admins;
use common\models\gateways\AdminsHash;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 * @package admin\models\forms
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    private $_user;

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password'], 'string'],
            [['username', 'password'], 'trim'],
            ['password', 'validatePassword'],
            ['username', 'validateStatus'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     * @return bool
     */
    public function validatePassword($attribute, $params)
    {
        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, Yii::t('admin', 'login.message_bad_login'));

            return false;
        }

        return true;
    }

    /**
     * Validate admin status: suspended or active
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function validateStatus($attribute, $params)
    {
        $user = $this->getUser();

        if ($user && !$user->isActive()) {
            $this->addError($attribute, Yii::t('admin', 'login.message_suspended'));
            return false;
        }

        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool bool whether the user is logged in successfully
     * @throws \Exception
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();

        if (!$user) {
            return false;
        }

        $hash = $user::generateAuthKey($user->getId());

        AdminsHash::deleteByHash($hash);
        AdminsHash::setHash($user->id, $hash, AdminsHash::MODE_SUPERADMIN_OFF);

        if (!Yii::$app->user->login($user, Admins::COOKIE_LIFETIME)) {
            return false;
        }

        $user->auth_key = Admins::generateAuthKey($user->id);
        $user->ip = Yii::$app->getRequest()->getUserIP();
        $user->last_login = time();
        $user->save(false);

        return true;
    }

    /**
     * Finds user by Username
     * @return Admins|null
     */
    public function getUser()
    {
        if (!($this->_user instanceof Admins)) {
            $this->_user = Admins::findByUsername($this->username);
        }

        return $this->_user;
    }

}