<?php

namespace control_panel\models\forms;

use common\models\sommerces\StoreAdminAuth;
use control_panel\helpers\UserHelper;
use common\models\sommerces\MyActivityLog;
use Yii;
use yii\base\Model;

/**
 * Class EditStoreStaffForm
 * @package control_panel\models\forms
 */
class EditStoreStaffForm extends Model
{
    public $account;
    public $status;
    public $access = [];

    /**
     * @var StoreAdminAuth
     */
    public $_staff;

    /** @inheritdoc */
    public function formName()
    {
        return 'EditStaffForm';
    }

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
            ['account', 'match', 'pattern' => '/^[a-z0-9-_@.]*$/i'],
            [['account', 'status'], 'required'],
            [['account'], 'string', 'min' => 3, 'max' => 32],
            [['status'], 'in', 'range' => array_keys(StoreAdminAuth::getStatuses())],
            [['account'], 'uniqueAccount'],
            [['access'], 'safe'],
        ];
    }

    /**
     * Set staff
     * @param StoreAdminAuth $staff
     */
    public function setStaff(StoreAdminAuth $staff)
    {
        $this->_staff = $staff;
    }

    /**
     * Validate account
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function uniqueAccount($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        if (StoreAdminAuth::find()->andWhere('username = :username AND store_id = :store_id AND id <> :id', [
            ':username' => $this->$attribute,
            ':id' => $this->_staff->id,
            ':store_id' => $this->_staff->store_id
        ])->exists()) {
            $this->addError($attribute, Yii::t('app', 'error.stores.staff.already_exist'));
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

        $this->_staff->username = $this->account;
        $this->_staff->status = (int)$this->status;
        $this->_staff->setRules((array)$this->access);

        $dirtyAttributes = $this->_staff->getDirtyAttributes();

        if (!$this->_staff->save()) {
            $this->addErrors($this->_staff->getErrors());
            return false;
        }

        if (isset($dirtyAttributes['username'])) {
            $this->_staff->logout();
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
            'account' => Yii::t('app', 'form.edit_store_staff.account'),
            'status' => Yii::t('app', 'form.edit_store_staff.status'),
        ];
    }

    /**
     * Get access list
     * @return array
     */
    public function getAccessRules()
    {
        return StoreAdminAuth::getRulesLabels();
    }

    /**
     * Write changes to log
     * @param $model StoreAdminAuth
     * @param $changedAttributes
     * @return bool
     */
    private function _changeLog($model, $changedAttributes)
    {
        // Changelog Staff rules changes
        if (
            isset($changedAttributes['rules'])
        ) {
            MyActivityLog::log(MyActivityLog::E_STORE_UPDATE_STAFF_ACCOUNT_RULES,
                $model->id, $model->id, UserHelper::getHash()
            );
        }

        // Changelog Staff lonin name changes
        if (isset($changedAttributes['username'])) {
            MyActivityLog::log(MyActivityLog::E_STORE_UPDATE_STAFF_ACCOUNT_NAME,
                $model->id, $model->id, UserHelper::getHash()
            );
        }

        // Changelog Staff status changes
        if (isset($changedAttributes['status'])) {
            MyActivityLog::log(MyActivityLog::E_STORE_UPDATE_STAFF_ACCOUNT_STATUS,
                $model->id, $model->id, UserHelper::getHash()
            );
        }
    }

}