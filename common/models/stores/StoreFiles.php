<?php

namespace common\models\stores;

use Yii;

/**
 * This is the model class for table "{{%store_files}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property integer $type
 * @property string $date
 * @property integer $created_at
 *
 * @property Stores $store
 */
class StoreFiles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%store_files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'type', 'created_at'], 'integer'],
            [['date'], 'string'],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'id']],
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
            'type' => Yii::t('app', '1 - logo, 2 - favicon'),
            'date' => Yii::t('app', 'Date'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['id' => 'store_id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\queries\StoreFilesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\stores\queries\StoreFilesQuery(get_called_class());
    }
}
