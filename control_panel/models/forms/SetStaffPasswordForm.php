<?php
namespace control_panel\models\forms;

use control_panel\helpers\UserHelper;
use common\models\panels\MyActivityLog;
use common\models\panels\ProjectAdmin;
use Yii;
use yii\base\Model;

/**
 * Class SetStaffPasswordForm
 * @package control_panel\models\forms
 */
class SetStaffPasswordForm extends Model
{
    public $password;
    public $username;

    /**
     * @var ProjectAdmin
     */
    private $_staff;

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
     * @param ProjectAdmin $staff
     */
    public function setStaff(ProjectAdmin $staff)
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

        // Changelog
        $project = $this->_staff->project;

        MyActivityLog::log((bool)$project->child_panel ?
            MyActivityLog::E_CHILD_PANEL_UPDATE_STAFF_ACCOUNT_PASSWORD :
            MyActivityLog::E_PANEL_UPDATE_STAFF_ACCOUNT_PASSWORD,
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
