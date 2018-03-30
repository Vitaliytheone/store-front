<?php

namespace common\models\panels;

use Yii;

/**
 * This is the model class for table "payment_hash".
 *
 * @property integer $id
 * @property string $hash
 */
class PaymentHash extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.payment_hash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hash'], 'string', 'max' => 10000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hash' => 'Hash',
        ];
    }
}
