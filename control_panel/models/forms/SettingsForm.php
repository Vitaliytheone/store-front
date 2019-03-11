<?php

namespace control_panel\models\forms;

use control_panel\helpers\UserHelper;
use common\models\sommerces\Customers;
use common\models\sommerces\MyActivityLog;
use Yii;
use yii\base\Model;

/**
 * Class SettingsForm
 * @package control_panel\models\forms
 */
class SettingsForm extends Model
{
    public $first_name;
    public $last_name;
    public $timezone;
    public $email;
    public $password;

    /**
     * @var Customers
     */
    private $_customer;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], // Clear for XSS
            [['first_name', 'last_name'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) || is_numeric($value) ? trim((string)$value) : null;
            }],
            [['first_name', 'last_name', 'timezone'], 'required'],
            [['timezone'], 'in', 'range' => array_keys($this->getTimezones()), 'message' => Yii::t('app', 'error.settings.bad_timezone')],
        ];
    }

    /**
     * Set customer
     * @param Customers $customer
     */
    public function setCustomer(Customers $customer)
    {
        $this->_customer = $customer;

        $this->email = $customer->email;
        $this->timezone = $customer->timezone;
        $this->first_name = $customer->first_name;
        $this->last_name = $customer->last_name;
    }

    /**
     * Sign up method
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_customer->first_name = $this->first_name;
        $this->_customer->last_name = $this->last_name;
        $this->_customer->timezone = (int)$this->timezone;

        $dirtyAttributes = $this->_customer->getDirtyAttributes();

        if (!$this->_customer->update()) {
            $this->addErrors($this->_customer->getErrors());
            return false;
        }

        $this->_changeLog($this->_customer, $dirtyAttributes);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'first_name' => Yii::t('app', 'form.settings.first_name'),
            'last_name' => Yii::t('app', 'form.settings.last_name'),
            'timezone' => Yii::t('app', 'form.settings.timezone'),
            'email' => Yii::t('app', 'form.settings.email'),
            'password' => Yii::t('app', 'form.settings.password'),
        ];
    }

    /**
     * Get available timezones
     * @return mixed
     */
    public function getTimezones()
    {
        return Yii::$app->params['timezones'];
    }

    /**
     * Write changes to log
     * @param $model Customers
     * @param $changedAttributes
     * @return bool
     */
    private function _changeLog($model, $changedAttributes)
    {
        if (isset($changedAttributes['first_name'])) {
            MyActivityLog::log(MyActivityLog::E_SETTINGS_UPDATE_FIRST_NAME, $model->id, $model->id, UserHelper::getHash());
        }

        if (isset($changedAttributes['last_name'])) {
            MyActivityLog::log( MyActivityLog::E_SETTINGS_UPDATE_LAST_NAME, $model->id, $model->id, UserHelper::getHash());
        }

        if (isset($changedAttributes['timezone'])) {
            MyActivityLog::log(MyActivityLog::E_SETTINGS_UPDATE_TIMEZONE, $model->id, $model->id, UserHelper::getHash());
        }
    }
}
