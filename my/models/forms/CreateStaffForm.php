<?php
namespace my\models\forms;

use my\helpers\UserHelper;
use common\models\panels\MyActivityLog;
use common\models\panels\Project;
use common\models\panels\ProjectAdmin;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class CreateStaffForm
 * @package my\models\forms
 */
class CreateStaffForm extends Model
{
    public $account;
    public $password;
    public $status;
    public $access = [];

    /**
     * @var Project
     */
    public $_project;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['account'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], // Clear for XSS
            [['account', 'password'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) || is_numeric($value) ? trim((string)$value) : null;
            }],
            ['account', 'match', 'pattern' => '/^[a-z0-9-_@.]*$/i'],
            [['account'], 'filter', 'filter' => 'strtolower'], // Strtolower
            [['account', 'password', 'status'], 'required'],
            [['password'], 'string', 'min' => 5, 'max' => 20],
            [['account'], 'string', 'min' => 3, 'max' => 32],
            [['status'], 'in', 'range' => array_keys(ProjectAdmin::getStatuses())],
            [['account'], 'uniqAccount'],
            [['access'], 'safe'],
        ];
    }

    /**
     * Set project
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->_project = $project;
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

        if (ProjectAdmin::findOne([
            'login' => $this->$attribute,
            'pid' => $this->_project->id
        ])) {
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

        $model = new ProjectAdmin();
        $model->setRules((array)$this->access);
        $model->pid = $this->_project->id;
        $model->login = $this->account;
        $model->status = $this->status;
        $model->setPassword($this->password);

        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        MyActivityLog::log((bool)$this->_project->child_panel ?
            MyActivityLog::E_CHILD_PANEL_CREATE_STAFF_ACCOUNT :
            MyActivityLog::E_PANEL_CREATE_STAFF_ACCOUNT,
            $model->id, $model->id, UserHelper::getHash()
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account' => Yii::t('app', 'form.create_staff.account'),
            'status' => Yii::t('app', 'form.create_staff.account'),
            'password' => Yii::t('app', 'form.create_staff.password'),
        ];
    }

    /**
     * Get access list
     * @return array
     */
    public function getAccessRules(): array
    {
        $labels = ProjectAdmin::getRulesLabels();

        // 25.02.2019 Александр http://prntscr.com/mpi176
        //надо пока закоментировать это, сделайте это срочно

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
            //'tools' => ArrayHelper::getValue($labels, 'tools'),
            'affiliates' => ArrayHelper::getValue($labels, 'affiliates'),
            'appearance' => ArrayHelper::getValue($labels, 'appearance'),
            'appearance_themes' => ArrayHelper::getValue($labels, 'appearance_themes'),
            'appearance_languages' => ArrayHelper::getValue($labels, 'appearance_languages'),
            'settings' => ArrayHelper::getValue($labels, 'settings'),
            'settings_general' => ArrayHelper::getValue($labels, 'settings_general'),
            'settings_providers' => ArrayHelper::getValue($labels, 'settings_providers'),
            'settings_payments' => ArrayHelper::getValue($labels, 'settings_payments'),
            'settings_bonuses' => ArrayHelper::getValue($labels, 'settings_bonuses'),
            'settings_pages' => ArrayHelper::getValue($labels, 'settings_pages'),
            'settings_menu' => ArrayHelper::getValue($labels, 'settings_menu'),
            'settings_preferences' => ArrayHelper::getValue($labels, 'settings_preferences'),
            'providers' => ArrayHelper::getValue($labels, 'providers'),
        ];
    }

    /**
     * Get access list of wrapped items
     * @return array
     */
    public function getWrappedRules(): array
    {
        return [
            'settings_general' => 'settings',
            'settings_providers' => 'settings',
            'settings_payments' => 'settings',
            'settings_bonuses' => 'settings',
            'settings_pages' => 'settings',
            'settings_menu' => 'settings',
            'settings_preferences' => 'settings',
            'appearance_themes' => 'appearance',
            'appearance_languages' => 'appearance',
        ];
    }
}
