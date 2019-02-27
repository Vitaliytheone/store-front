<?php

namespace common\models\sommerces;

use Yii;
use yii\db\ActiveRecord;
use common\models\sommerces\queries\StoresSendOrdersQuery;

/**
 * This is the model class for table "{{%stores_send_orders}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property integer $provider_id
 * @property string $store_db
 * @property integer $suborder_id
 */
class StoresSendOrders extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_STORES . '.stores_send_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'provider_id', 'suborder_id'], 'integer'],
            [['store_db'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'store_id' => Yii::t('app', 'Store ID'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'store_db' => Yii::t('app', 'Store Db'),
            'suborder_id' => Yii::t('app', 'Suborder ID'),
        ];
    }

    /**
     * @inheritdoc
     * @return StoresSendOrdersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoresSendOrdersQuery(get_called_class());
    }
}
