<?php

namespace common\models\panels;

use Yii;

/**
 * This is the model class for table "ticket_notes".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $note
 * @property int $created_at
 * @property int $updated_at
 */
class TicketNotes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_notes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'note'], 'required'],
            [['customer_id', 'created_at', 'updated_at'], 'integer'],
            [['note'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'note' => Yii::t('app', 'Note'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
