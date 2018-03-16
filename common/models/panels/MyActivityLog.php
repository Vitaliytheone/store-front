<?php

namespace common\models\panels;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\Controller;

/**
 * This is the model class for table "my_activity_log".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $super_user
 * @property integer $created_at
 * @property string $ip
 * @property string $controller
 * @property string $action
 * @property string $request_data
 * @property string $details
 * @property string $details_id
 * @property integer $event
 */
class MyActivityLog extends ActiveRecord
{
    // Events constants
    const E_PANEL_CREATE_STAFF_ACCOUNT = 101;
    const E_PANEL_UPDATE_STAFF_ACCOUNT_NAME = 102;
    const E_PANEL_UPDATE_STAFF_ACCOUNT_STATUS = 103;
    const E_PANEL_UPDATE_STAFF_ACCOUNT_RULES = 104;
    const E_PANEL_UPDATE_STAFF_ACCOUNT_PASSWORD = 105;

    const E_CHILD_PANEL_CREATE_STAFF_ACCOUNT = 201;
    const E_CHILD_PANEL_UPDATE_STAFF_ACCOUNT_NAME = 202;
    const E_CHILD_PANEL_UPDATE_STAFF_ACCOUNT_STATUS = 204;
    const E_CHILD_PANEL_UPDATE_STAFF_ACCOUNT_RULES = 205;
    const E_CHILD_PANEL_UPDATE_STAFF_ACCOUNT_PASSWORD =	206;

    const E_SETTINGS_UPDATE_FIRST_NAME = 301;
    const E_SETTINGS_UPDATE_LAST_NAME = 302;
    const E_SETTINGS_UPDATE_EMAIL = 303;
    const E_SETTINGS_UPDATE_PASSWORD = 304;
    const E_SETTINGS_UPDATE_TIMEZONE = 305;

    const E_ORDERS_CREATE_PANEL_ORDER =	401;
    const E_ORDERS_CREATE_DOMAIN_ORDER = 402;
    const E_ORDERS_CREATE_SSL_ORDER = 403;
    const E_ORDERS_CREATE_CHILD_PANEL_ORDER = 404;

    const E_TICKETS_CREATE_TICKET = 501;
    const E_TICKETS_REPLY_TICKET = 502;

    const E_CUSTOMER_AUTHORIZATION = 601;
    const E_CUSTOMER_FORGOT_PASSWORD = 602;
    const E_SUPER_USER_AUTHORIZATION = 603;
    const E_CUSTOMER_REGISTRATION = 604;

    /** @var  Customers */
    private $_customer;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'my_activity_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'super_user', 'ip', 'controller', 'action', 'request_data', 'details', 'details_id', 'event'], 'required'],
            [['customer_id', 'super_user', 'created_at', 'event'], 'integer'],
            [['request_data'], 'string'],
            [['ip', 'controller', 'action'], 'string', 'max' => 300],
            [['details', 'details_id'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'super_user' => 'Super User',
            'created_at' => 'Created At',
            'ip' => 'Ip',
            'controller' => 'Controller',
            'action' => 'Action',
            'request_data' => 'Request Data',
            'details' => 'Details',
            'details_id' => 'Details ID',
            'event' => 'Event',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\queries\MyActivityLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\panels\queries\MyActivityLogQuery(get_called_class());
    }

    /**
     * Set current user
     * @param Customers $customer
     */
    public function setUser(Customers $customer)
    {
           $this->_customer = $customer;
    }

    /**
     * Create Activity log record only for Customers or Superadmin logged in like Customer
     *
     * You can identificate user by:
     * 1. $customerHash (Session Hash) for logged in user actions;
     * 2. $customerId for not logged in user actions like `restore password`;
     *
     * @param $customerHash string Session Hash of current customer
     * @param $eventId integer
     * @param $detailsId string|integer
     * @param $details string|integer
     * @param $customerId integer
     *
     * @return MyActivityLog|null
     */
    public static function log($eventId, $detailsId, $details, $customerHash, $customerId = null)
    {
        $isSuperuser = MyCustomersHash::TYPE_NOT_SUPER_USER;

        // Limit log records only for Customers or superadmin logged in like Customer
        if (empty($customerHash) && empty($customerId)) {
            return null;
        }

        /** @var Controller $controller */
        $controller = Yii::$app->controller;

        if (empty($controller) || !$controller instanceof Controller) {
            return null;
        }

        // Get Customer from CustomerHash
        if (!empty($customerHash)) {

            $hashModel = MyCustomersHash::findOne(['hash' => $customerHash]);

            if (empty($hashModel)) {
                return null;
            }

            /** @var Auth $customer */
            $customer = $hashModel->customer;

            if (empty($customer)) {
                return null;
            }

            $customerId = $customer->id;
            $isSuperuser = $hashModel->super_user;
        }

        $model = new self();

        $model->setAttributes([
            'customer_id' => $customerId,
            'super_user' => $isSuperuser,
            'event' => $eventId,
            'details_id' => (string)$detailsId,
            'details' => (string)$details,
            'ip' => Yii::$app->getRequest()->getUserIP(),
            'controller' => $controller->id,
            'action' => $controller->action->id,
            'request_data' => json_encode([$_SERVER, $_POST, $_GET]),
        ], false);

        return $model->save() ? $model : null;
    }

}
