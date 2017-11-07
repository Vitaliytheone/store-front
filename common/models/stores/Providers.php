<?php

namespace common\models\stores;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\stores\queries\ProvidersQuery;

/**
 * This is the model class for table "{{%providers}}".
 *
 * @property integer $id
 * @property string $site
 * @property integer $type
 * @property integer $created_at
 *
 * @property StoreProviders[] $storeProviders
 */
class Providers extends ActiveRecord
{
    const TYPE_INTERNAL = 0;
    const TYPE_EXTERNAL = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%providers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'created_at'], 'integer'],
            [['site'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'site' => Yii::t('app', 'Site'),
            'type' => Yii::t('app', 'Type'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreProviders()
    {
        return $this->hasMany(StoreProviders::className(), ['provider_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ProvidersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProvidersQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }
}
