<?php

namespace common\models\panels;

use Yii;

/**
 * This is the model class for table "panel_andreyglobincom.services".
 *
 * @property int $id
 * @property string $reid
 * @property string $params
 * @property int $type 0 - Default 1 - SEO 2 - Comments 3 - Mentions
 * @property int $res
 * @property int $mode 0 - Disabled, 1 - Enabled
 * @property int $refill
 * @property int $cancel
 * @property int $drip_feed
 * @property string $name
 * @property string $desc
 * @property string $price
 * @property int $overflow
 * @property int $increment
 * @property string $minprice
 * @property int $date
 * @property int $act 0 - member hide 1 - visible all 3 - admin and member hide
 * @property string $units
 * @property int $min
 * @property string $max
 * @property int $position
 * @property int $cid
 * @property int $validation
 * @property int $start_count
 * @property int $start_type
 * @property int $link_type
 * @property int $drip_feed_orders
 * @property int $provider_id res => provider_id 
 * @property string $provider_service_id reid => provider_service_id
 * @property string $provider_service_params
 * @property string $provider_rate
 * @property int $provider_min
 * @property int $provider_max
 * @property int $provider_notify 1 – provider turned off the service 
 * @property int $deleted
 */
class Services extends \yii\db\ActiveRecord
{
    const MODE_DISABLED = 0;
    const MODE_ENABLED = 0;

    const TYPE_DEFAULT = 0;
    const TYPE_SEO = 1;
    const TYPE_COMMENTS = 2;
    const TYPE_MENTIONS = 3;

    const ACT_MEMBER_HIDE = 0;
    const ACT_VISIBLE_ALL = 1;
    const ACT_ADMIN_AND_MEMBER_HIDE = 3;

    /**
     * @param $db string
     * @inheritdoc
     */
    public static function tableName($db)
    {
        return $db . '.services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reid', 'params', 'type', 'res', 'mode', 'drip_feed', 'name', 'desc', 'price', 'overflow', 'minprice', 'date', 'act', 'units', 'min', 'max', 'position', 'cid', 'validation', 'start_count', 'start_type', 'link_type'], 'required'],
            [['type', 'res', 'overflow', 'increment', 'date', 'act', 'min', 'position', 'cid', 'validation', 'start_count', 'start_type', 'link_type', 'provider_id', 'provider_min', 'provider_max'], 'integer'],
            [['price', 'minprice', 'provider_rate'], 'number'],
            [['reid', 'units'], 'string', 'max' => 300],
            [['params', 'name', 'provider_service_id', 'provider_service_params'], 'string', 'max' => 1000],
            [['mode'], 'string', 'max' => 4],
            [['refill', 'cancel', 'drip_feed', 'drip_feed_orders', 'provider_notify', 'deleted'], 'string', 'max' => 1],
            [['desc'], 'string', 'max' => 3000],
            [['max'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'reid' => Yii::t('app', 'Reid'),
            'params' => Yii::t('app', 'Params'),
            'type' => Yii::t('app', 'Type'),
            'res' => Yii::t('app', 'Res'),
            'mode' => Yii::t('app', 'Mode'),
            'refill' => Yii::t('app', 'Refill'),
            'cancel' => Yii::t('app', 'Cancel'),
            'drip_feed' => Yii::t('app', 'Drip Feed'),
            'name' => Yii::t('app', 'Name'),
            'desc' => Yii::t('app', 'Desc'),
            'price' => Yii::t('app', 'Price'),
            'overflow' => Yii::t('app', 'Overflow'),
            'increment' => Yii::t('app', 'Increment'),
            'minprice' => Yii::t('app', 'Minprice'),
            'date' => Yii::t('app', 'Date'),
            'act' => Yii::t('app', 'Act'),
            'units' => Yii::t('app', 'Units'),
            'min' => Yii::t('app', 'Min'),
            'max' => Yii::t('app', 'Max'),
            'position' => Yii::t('app', 'Position'),
            'cid' => Yii::t('app', 'Cid'),
            'validation' => Yii::t('app', 'Validation'),
            'start_count' => Yii::t('app', 'Start Count'),
            'start_type' => Yii::t('app', 'Start Type'),
            'link_type' => Yii::t('app', 'Link Type'),
            'drip_feed_orders' => Yii::t('app', 'Drip Feed Orders'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'provider_service_id' => Yii::t('app', 'Provider Service ID'),
            'provider_service_params' => Yii::t('app', 'Provider Service Params'),
            'provider_rate' => Yii::t('app', 'Provider Rate'),
            'provider_min' => Yii::t('app', 'Provider Min'),
            'provider_max' => Yii::t('app', 'Provider Max'),
            'provider_notify' => Yii::t('app', 'Provider Notify'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }
}
