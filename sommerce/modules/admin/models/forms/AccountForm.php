<?php
namespace sommerce\modules\admin\models\forms;

use common\models\sommerce\ActivityLog;
use common\models\sommerces\StoreAdminAuth;
use Yii;
use yii\base\Model;
use yii\web\User;

/**
 * Class AccountForm
 * @package sommerce\modules\admin\models\forms
 */
class AccountForm extends Model
{
    public $current_password;
    public $password;
    public $confirm_password;

    private $_user;

    /**
     * @return string
     */
    public function formName()
    {
        return 'AccountForm';
    }

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
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Return current user
     * @return User|null
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
        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity();

        if (!$identity || !$identity->validatePassword($this->current_password)) {
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

        /** @var StoreAdminAuth $identity */
        $identity = $user->getIdentity();

        if (!$user || !$this->validate()) {
            return false;
        }

        $identity->setPassword($this->password);

        if (!$identity->save(false)) {
            return false;
        }

        ActivityLog::log($identity, ActivityLog::E_ADMIN_PASSWORD_CHANGED);

        return true;
    }
}