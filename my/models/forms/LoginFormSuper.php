<?php
namespace my\models\forms;

use my\helpers\UserHelper;
use Yii;
use yii\base\Model;
use common\models\panels\Auth;

/**
 * LoginFormSuper is the model behind the login form.
 *
 * @property Auth|null $user This property is read-only.
 *
 */
class LoginFormSuper extends Model
{
    public $username;
    public $password;

    /**
     * @var Auth|boolean
     */
    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
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

            if (!$user || !$user->validatePassword($this->password, true)) {
                $this->addError('', Yii::t('app', 'error.login.incorrect_email_or_password'));
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
            $user->isSuperadminAuth = true;
            $auth = Yii::$app->user->login($this->getUser(), UserHelper::AUTH_DURATION);

            if ($auth) {
                $this->_user->setAuthKey(1, 1);
                // Сохраняем и автоматически обновляются два поля auth_ip и auth_date
                $this->_user->save(false);
                return true;
            }
        }
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
}
