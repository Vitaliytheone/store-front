<?php

namespace common\models\panels;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\SuperCreditsLogQuery;
use Yii;

/**
 * This is the model class for table "super_credits_log".
 *
 * @property integer $id
 * @property integer $super_admin_id
 * @property integer $invoice_id
 * @property string $credit
 * @property string $memo
 * @property integer $created_at
 */
class SuperCreditsLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.super_credits_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['super_admin_id', 'invoice_id', 'credit'], 'required'],
            [['super_admin_id', 'invoice_id', 'created_at'], 'integer'],
            [['credit'], 'number'],
            [['memo'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'super_admin_id' => 'Super Admin ID',
            'invoice_id' => 'Invoice ID',
            'credit' => 'Credit',
            'memo' => 'Memo',
            'created_at' => 'Created At',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
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
     * @return SuperCreditsLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SuperCreditsLogQuery(get_called_class());
    }
}
