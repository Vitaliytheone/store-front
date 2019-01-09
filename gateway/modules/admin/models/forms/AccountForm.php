<?php
namespace admin\models\forms;

use common\models\gateways\Admins;
use Yii;
use yii\base\Model;
use yii\web\User;

/**
 * Class AccountForm
 * @package admin\models\forms
 */
class AccountForm extends Model
{
    public $current_password;
    public $password;
    public $confirm_password;

    private $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['current_password', 'password', 'confirm_password'], 'required'],
            [['current_password', 'password', 'confirm_password'], 'string'],
            [['current_password', 'password', 'confirm_password'], 'trim'],
            ['password', 'compare', 'compareAttribute' => 'confirm_password', 'message' => Yii::t('admin', 'account.message_wrong_new_password_pair')],
            ['password', 'compare', 'compareAttribute' => 'current_password', 'operator' => '!=', 'message' => Yii::t('admin', 'account.message_wrong_new_password')],
            ['current_password', 'validatePassword']
        ];
    }

    /**
     * Set current user object
     * @param Admins $user
     */
    public function setUser(Admins $user)
    {
        $this->_user = $user;
    }

    /**
     * Return current user
     * @return Admins|null
     */
    public function getUser()
    {
        return $this->_user;
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
        /** @var Admins $identity */
        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->current_password)) {
            $this->addError($attribute, Yii::t('admin', 'account.message_wrong_current_password'));

            return false;
        }

        return true;
    }

    /**
     * Change current admin password and autologin user
     * @return bool
     */
    public function changePassword()
    {
        $user = $this->getUser();

        if (!$user || !$this->validate()) {
            return false;
        }

        $user->setPassword($this->password);

        if (!$user->save(false)) {
            return false;
        }

        return true;
    }
}