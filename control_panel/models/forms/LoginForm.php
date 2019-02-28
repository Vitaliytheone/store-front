<?php
namespace control_panel\models\forms;

use control_panel\helpers\UserHelper;
use common\models\panels\MyActivityLog;
use Yii;
use yii\base\Model;
use common\models\panels\Auth;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
use yii\helpers\FileHelper;

/**
 * Class LoginForm
 * @package control_panel\models\forms
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $re_captcha;

    /**
     * @var bool|Auth
     */
    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $rules = [];

        if ($this->isCheckCaptcha() && 'test' != YII_ENV) {
            $rules[] = [['re_captcha'], ReCaptchaValidator::className(), 'uncheckedMessage' => Yii::t('app', 'error.login.incorrect_captcha'), 'message' => Yii::t('app', 'error.login.incorrect_captcha')];
        }

        $rules = array_merge($rules, [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
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
                $this->addError('username', Yii::t('app', 'error.login.incorrect_email_or_password'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {   
        if ($this->validate()) {
            $user = $this->getUser();

            $auth = Yii::$app->user->login($user);

            if ($auth) {
                $user->setAuthKey();
                // Сохраняем и автоматически обновляются два поля auth_ip и auth_date
                $user->save(false);

                static::removeFail();

                MyActivityLog::log(MyActivityLog::E_CUSTOMER_AUTHORIZATION, $user->id, $user->id, UserHelper::getHash());

                return true;
            }
        }

        static::addFail();

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return Auth|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Auth::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Check or not user auth with re captcha
     * @return bool
     */
    public function isCheckCaptcha()
    {
        if (!static::checkFail()) {
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'form.signin.email'),
            'password' => Yii::t('app', 'form.signin.password'),
        ];
    }
}
