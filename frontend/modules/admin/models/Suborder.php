<?php

namespace frontend\modules\admin\models;

use Yii;

/**
 * Class Suborder
 *
 * Uses scenarios
 * $model = new Suborder(['scenario' => Order::SCENARIO_CREATE]);
 * Available follow scenarios:
 * SCENARIO_CREATE used to create new model & DB record
 * SCENARIO_UPDATE used to update exiting model & DB record
 *
 * @property $id
 * @property $order_id
 * @property $checkout_id
 * @property $link
 * @property $amount
 * @property $package_id
 * @property $quantity
 * @property $status
 * @property $updated_at
 * @property $mode
 * @property $provider_id
 * @property $provider_service
 * @property $provider_order_id
 * @property $provider_charge
 * @property $provider_response
 */


class Suborder extends \yii\db\ActiveRecord
{
    const STATUS_AWAITING       = 1;
    const STATUS_PENDING        = 2;
    const STATUS_IN_PROGRESS    = 3;
    const STATUS_COMPLETED      = 4;
    const STATUS_CANCELED       = 5;
    const STATUS_FAILED         = 6;
    const STATUS_ERROR          = 7;

    const MODE_MANUAL           = 0;
    const MODE_AUTO             = 1;
    
    // Provider API Suborder statuses
    const PROVIDER_ORDER_STATUS_PENDING     = 'Pending';
    const PROVIDER_ORDER_STATUS_IN_PROGRESS = 'In progress';
    const PROVIDER_ORDER_STATUS_COMPLETED   = 'Completed';
    const PROVIDER_ORDER_STATUS_PARTIAL     = 'Partial';
    const PROVIDER_ORDER_STATUS_CANCELED    = 'Canceled';
    const PROVIDER_ORDER_STATUS_PROCESSING  = 'Processing';

    // Relations Suborder Status to Provider Order Status
    static $suborderStatusRelations = [
        self::STATUS_PENDING       =>  self::PROVIDER_ORDER_STATUS_PENDING,
        self::STATUS_IN_PROGRESS   =>  self::PROVIDER_ORDER_STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED     =>  self::PROVIDER_ORDER_STATUS_COMPLETED,
        self::STATUS_CANCELED      =>  self::PROVIDER_ORDER_STATUS_CANCELED,
        self::STATUS_ERROR         =>  [ self::PROVIDER_ORDER_STATUS_PARTIAL, self::PROVIDER_ORDER_STATUS_PROCESSING ],
    ];

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    
    /**
     * Model init routine
    */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $db = yii::$app->store->getInstance()->db_name;
        return "{{%$db.suborders}}";
    }

    /**
     * Define On_insert &  On_update hooks
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return true;
        }
        if ($insert) {
            // Set Status by Mode
            $status = ($this->mode == self::MODE_MANUAL) ? self::STATUS_PENDING : self::STATUS_AWAITING;
            $this->setAttribute('status', $status);
        } else {
            $this->setAttribute('updated_at', time());
        }
        return true;
    }

    /**
     * Define validation rules
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'checkout_id', 'package_id', 'quantity', 'status', 'updated_at', 'mode', 'provider_id'], 'integer'],
            [['amount', 'provider_charge'], 'number'],
            [['provider_response'], 'string'],
            [['link'], 'string', 'max' => 1000],
            [['provider_service', 'provider_order_id'], 'string', 'max' => 300],
        ];
    }

    /**
     * Get Suborder status by Provider`s Order status
     * @param $providerOrderStatus
     * @return false|int
     */
    public static function getStatusByProviderStatus($providerOrderStatus)
    {
        $providerOrderStatus = strtolower($providerOrderStatus);
        $result = null;
        foreach(self::$suborderStatusRelations as $orderStatus => $providerStatus) {
            if (is_array($providerStatus)) {
                $searchResult = array_search($providerOrderStatus, array_map('strtolower', $providerStatus));
                $result = $searchResult !== false;
            } else {
                $compareResult = strcasecmp($providerOrderStatus, $providerStatus) == 0;
                $result = $compareResult ? $orderStatus : false;
            }
            if ($result) return $orderStatus;
        }
        return false;
    }

    /**
     * Return Suborder Details for UI purpose
     *
     * Defines is returned provider response
     * plain Json string or print_r formatted string
     * @param bool $printR
     * @return array|null
     */
    public function getDetails(bool $printR = true)
    {
        $provider = (new \yii\db\Query())
            ->select(['site'])
            ->from("providers")
            ->where(['id' => $this->provider_id])
            ->one();
        if (!$provider) {
            return null;
        }
        $providerResponse = $this->provider_response;
        if ($printR) {
            $providerResponse = print_r(json_decode($providerResponse),1);
        }
        $orderDetails = [
            'provider' => $provider['site'],
            'provider_order_id' => $this->provider_order_id,
            'provider_response' => $providerResponse,
            'updated_at' => $this->updated_at,
        ];
        return $orderDetails;
    }

}