<?php

namespace common\models\panels;

use Yii;

/**
 * This is the model class for table "order_logs".
 *
 * @property integer $id
 * @property integer $cid
 * @property string $domain
 * @property integer $date
 * @property string $log
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
            'cid' => 'Cid',
            'domain' => 'Domain',
            'date' => 'Date',
            'log' => 'Log',
        ];
    }
}
