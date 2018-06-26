<?php

namespace my\modules\superadmin\models\forms;

use common\models\panels\AuthFails;
use common\models\panels\SuperAdmin;
use common\models\panels\SuperLog;
use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
use yii\helpers\FileHelper;

/**
 * LoginForm is the model behind the login form.
 *
 * @property SuperAdmin|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    const AUTH_DURATION = 31536000; // 3600 * 24 * 365 - 1 год

    public $username;
    public $password;
    public $re_captcha;

    /**
     * @var bool|SuperAdmin
     */
    private $_user = false;

    private $_ip;

    public function __construct(array $config = [])
    {
        $this->_ip = Yii::$app->request->userIp;

        parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $rules = [];

        if ($this->isCheckCaptcha()) {
            $rules[] = [['re_captcha'], ReCaptchaValidator::class, 'uncheckedMessage' => 'Please confirm that you are not a bot.'];
        }

        $rules = array_merge($rules, [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
            ['username', 'validateStatus'],
        ]);

        return $rules;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Validates the status.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateStatus($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user && SuperAdmin::STATUS_ACTIVE != $user->status) {
                $this->addError($attribute, 'User is not active.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $this->removeFail();
            $auth = Yii::$app->superadmin->login($this->getUser(), static::AUTH_DURATION);

            if ($auth) {
                SuperLog::log($this->_user->id, SuperLog::ACTION_AUTH);
            }

            return $auth;
        }

        $this->addFail();
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return SuperAdmin|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = SuperAdmin::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Check or not user auth with re captcha
     * @return bool
     */
    public function isCheckCaptcha()
    {
        if (!$this->checkFail()) {
            return false;
        }

        return true;
    }

    /**
     * Add fail auth
     */
    private static function addFail()
    {
        $path = Yii::getAlias('@runtime/secret/');

        if (!is_dir($path)) {
            FileHelper::createDirectory($path);
        }

        @file_put_contents($path . Yii::$app->request->userIp . '.log', '1');
    }

    /**
     * Check is show captcha
     * @return bool
     */
    private static function checkFail()
    {
        $path = Yii::getAlias('@runtime/secret/');

        return file_exists($path . Yii::$app->request->userIp .'.log');
    }

    /**
     * Remove fail auth
     */
    private static function removeFail()
    {
        if (!static::checkFail()) {
            return;
        }

        $path = Yii::getAlias('@runtime/secret/');

        unlink($path . Yii::$app->request->userIp .'.log');
    }
}
