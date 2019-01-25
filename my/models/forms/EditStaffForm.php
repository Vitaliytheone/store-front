<?php
namespace my\models\forms;

use my\helpers\UserHelper;
use common\models\panels\MyActivityLog;
use common\models\panels\ProjectAdmin;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class EditStaffForm
 * @package my\models\forms
 */
class EditStaffForm extends Model
{
    public $account;
    public $status;
    public $access = [];

    /**
     * @var ProjectAdmin
     */
    public $_staff;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['account'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], // Clear for XSS
            [['account'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) || is_numeric($value) ? trim((string)$value) : null;
            }],

            [['account', 'status'], 'required'],
            [['account'], 'string', 'min' => 5, 'max' => 20],
            [['status'], 'in', 'range' => array_keys(ProjectAdmin::getStatuses())],
            [['account'], 'uniqAccount'],
            [['access'], 'safe'],
        ];
    }

    /**
     * Set staff
     * @param ProjectAdmin $staff
     */
    public function setStaff(ProjectAdmin $staff)
    {
        $this->_staff = $staff;
    }

    /**
     * Validate account
     * @param $attribute
     * @param $params
     */
    public function uniqAccount($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        if (ProjectAdmin::find()->andWhere('login = :login AND pid = :pid AND id <> :id', [
            ':login' => $this->$attribute,
            ':id' => $this->_staff->id,
            ':pid' => $this->_staff->pid
        ])->exists()) {
            $this->addError($attribute, Yii::t('app', 'error.staff.already_exist'));
            return false;
        }
    }

    /**
     * Create ticket method
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_staff->login = $this->account;
        $this->_staff->status = (int)$this->status;
        $this->_staff->setRules((array)$this->access);
        $this->_staff->date = time();
        $this->_staff->update_at = time();

        $dirtyAttributes = $this->_staff->getDirtyAttributes();

        if (!$this->_staff->save()) {
            $this->addErrors($this->_staff->getErrors());
            return false;
        }

        $this->_changeLog($this->_staff, $dirtyAttributes);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account' => Yii::t('app', 'form.edit_staff.account'),
            'status' => Yii::t('app', 'form.edit_staff.account')
        ];
    }

    /**
     * Get access list
     * @return array
     */
    public function getAccessRules()
    {
        $labels = ProjectAdmin::getRulesLabels();
        return [
            'users' => ArrayHelper::getValue($labels, 'users'),
            'orders' => ArrayHelper::getValue($labels, 'orders'),
            'subscription' => ArrayHelper::getValue($labels, 'subscription'),
            'tasks' => ArrayHelper::getValue($labels, 'tasks'),
            'dripfeed' => ArrayHelper::getValue($labels, 'dripfeed'),
            'services' => ArrayHelper::getValue($labels, 'services'),
            'payments' => ArrayHelper::getValue($labels, 'payments'),
            'tickets' => ArrayHelper::getValue($labels, 'tickets'),
            'reports' => ArrayHelper::getValue($labels, 'reports'),
            'affiliates' => ArrayHelper::getValue($labels, 'affiliates'),
            'providers' => ArrayHelper::getValue($labels, 'providers'),
            'settings' => ArrayHelper::getValue($labels, 'settings'),
        ];
    }

    /**
     * Get access list
     * @return array
     */
    public function getAccessSettings()
    {
        $labels = ProjectAdmin::getRulesLabels();

        return [
            'settings_general' => ArrayHelper::getValue($labels, 'settings_general'),
            'settings_providers' => ArrayHelper::getValue($labels, 'settings_providers'),
            'settings_payments' => ArrayHelper::getValue($labels, 'settings_payments'),
            'settings_bonuses' => ArrayHelper::getValue($labels, 'settings_bonuses'),
            'settings_pages' => ArrayHelper::getValue($labels, 'settings_pages'),
            'settings_menu' => ArrayHelper::getValue($labels, 'settings_menu'),
            'settings_preferences' => ArrayHelper::getValue($labels, 'settings_preferences'),
            'settings_themes' => ArrayHelper::getValue($labels, 'settings_themes'),
            'settings_languages' => ArrayHelper::getValue($labels, 'settings_languages'),
        ];
    }

    /**
     * Write changes to log
     * @param $model ProjectAdmin
     * @param $changedAttributes
     * @return bool
     */
    private function _changeLog($model, $changedAttributes)
    {
        $project = $model->project;

        // Changelog Staff rules changes
        if (
            isset($changedAttributes['rules'])
        ) {
            MyActivityLog::log((bool)$project->child_panel ?
                MyActivityLog::E_CHILD_PANEL_UPDATE_STAFF_ACCOUNT_RULES :
                MyActivityLog::E_PANEL_UPDATE_STAFF_ACCOUNT_RULES,
                $model->id, $model->id, UserHelper::getHash()
            );
        }

        // Changelog Staff lonin name changes
        if (isset($changedAttributes['login'])) {
            MyActivityLog::log($project->child_panel ?
                MyActivityLog::E_CHILD_PANEL_UPDATE_STAFF_ACCOUNT_NAME :
                MyActivityLog::E_PANEL_UPDATE_STAFF_ACCOUNT_NAME,
                $model->id, $model->id, UserHelper::getHash()
            );
        }

        // Changelog Staff status changes
        if (isset($changedAttributes['status'])) {
            MyActivityLog::log($project->child_panel ?
                MyActivityLog::E_CHILD_PANEL_UPDATE_STAFF_ACCOUNT_STATUS :
                MyActivityLog::E_PANEL_UPDATE_STAFF_ACCOUNT_STATUS,
                $model->id, $model->id, UserHelper::getHash()
            );
        }
    }

}