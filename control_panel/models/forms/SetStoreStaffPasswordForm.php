<?php
namespace control_panel\models\forms;

use common\models\sommerces\StoreAdminAuth;
use control_panel\helpers\UserHelper;
use common\models\panels\MyActivityLog;
use Yii;
use yii\base\Model;

/**
 * Class SetStoreStaffPasswordForm
 * @package control_panel\models\forms
 */
class SetStoreStaffPasswordForm extends Model
{
    public $password;
    public $username;

    /**
     * @var StoreAdminAuth
     */
    private $_staff;

    /** @inheritdoc */
    public function formName()
    {
        return 'SetStaffPasswordForm';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) || is_numeric($value) ? trim((string)$value) : null;
            }],
            [['password'], 'required'],
            [['password'], 'string', 'max' => 20, 'min' => 5],
        ];
    }

    /**
     * Set staff user
     * @param StoreAdminAuth $staff
     */
    public function setStaff(StoreAdminAuth $staff)
    {
        $this->_staff = $staff;
    }

    /**
     * Save project admin password
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_staff->setPassword($this->password);

        if (!$this->_staff->save()) {
            $this->addErrors($this->_staff->getErrors());
            return false;
        }

        $this->_staff->logout();

        MyActivityLog::log(MyActivityLog::E_STORE_UPDATE_STAFF_ACCOUNT_PASSWORD,
            $this->_staff->id, $this->_staff->id, UserHelper::getHash()
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'form.staff_password.password'),
            'username' => Yii::t('app', 'form.staff_password.username'),
        ];
    }
}
