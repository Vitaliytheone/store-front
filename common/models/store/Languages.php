<?php

namespace common\models\store;

use Yii;
use \yii\db\Connection;
use \yii\db\ActiveRecord;
use \common\models\store\queries\LanguagesQuery;

/**
 * This is the model class for table "{{languages}}".
 *
 * @property integer $id
 * @property string $code
 * @property integer $created_at
 * @property integer $updated_at
 */
class Languages extends ActiveRecord
{
    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%languages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['code'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Language code in IETF lang format'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return LanguagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LanguagesQuery(get_called_class());
    }
}
