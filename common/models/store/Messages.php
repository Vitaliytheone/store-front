<?php

namespace common\models\store;

use Yii;
use \yii\db\Connection;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%messages}}".
 *
 * @property integer $id
 * @property string $lang_code
 * @property string $section
 * @property string $name
 * @property string $value
 */
class Messages extends ActiveRecord
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
        return '{{%messages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lang_code'], 'string', 'max' => 10],
            [['section'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 500],
            [['value'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'lang_code' => Yii::t('app', 'Language code in IETF lang format'),
            'section' => Yii::t('app', 'Message section'),
            'name' => Yii::t('app', 'Message variable name'),
            'value' => Yii::t('app', 'Message text'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\store\queries\Messages the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\store\queries\Messages(get_called_class());
    }
}
