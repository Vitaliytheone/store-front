<?php

namespace common\models\panels;

use Yii;

/**
 * This is the model class for table "order_logs".
 *
 * @property integer $id
 * @property string $title
 * @property string $price
 * @property string $description
 * @property integer $of_orders
 * @property integer $before_orders
 */
class OrderLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.order_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'date'], 'integer'],
            [['domain', 'log'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'price' => 'Price',
            'description' => 'Description',
            'of_orders' => 'Of Orders',
            'before_orders' => 'Before Orders',
        ];
    }
}
