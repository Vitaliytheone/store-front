<?php
namespace my\models\forms;

use common\models\stores\StoreAdminAuth;
use common\models\stores\StoreAdmins;
use common\models\stores\Stores;
use my\helpers\UserHelper;
use common\models\panels\MyActivityLog;
use Yii;
use yii\base\Model;

/**
 * Class CreateStoreStaffForm
 * @package my\models\forms
 */
class CreateStoreStaffForm extends Model
{
    public $account;
    public $password;
    public $status;
    public $access = [];

    /**
     * @var Stores
     */
    public $_store;

    /** @inheritdoc */
    public function formName()
    {
        return 'CreateStaffForm';
    }

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
            [['status'], 'in', 'range' => array_keys(StoreAdmins::getStatuses())],
            [['account'], 'uniqueAccount'],
            [['access'], 'safe'],
        ];
    }

    /**
     * Set store
     * @param $store Stores
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Account unique validator
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function uniqueAccount($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        if (StoreAdmins::findOne([
            'username' => $this->$attribute,
            'store_id' => $this->_store->id
        ])) {
            $this->addError($attribute, Yii::t('app', 'error.stores.staff.already_exist'));
            return false;
        }
    }

    /**
     * Create staff account
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $model = new StoreAdminAuth();
        $model->setRules($this->access);
        $model->store_id = $this->_store->id;
        $model->username = $this->account;
        $model->status = $this->status;
        $model->setPassword($this->password);

        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        MyActivityLog::log(MyActivityLog::E_STORE_CREATE_STAFF_ACCOUNT,
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
            'account' => Yii::t('app', 'form.create_store_staff.account'),
            'status' => Yii::t('app', 'form.create_store_staff.status'),
            'password' => Yii::t('app', 'form.create_store_staff.password'),
        ];
    }

    /**
     * Get access list
     * @return array
     */
    public function getAccessRules()
    {
        return StoreAdmins::getRulesLabels();
    }
}