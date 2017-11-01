<?php

namespace common\models\stores;

use Yii;

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
class Providers extends \yii\db\ActiveRecord
{
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
            'type' => Yii::t('app', '0 - internal, 1 - external'),
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
     * @return \common\models\stores\queries\ProvidersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\stores\queries\ProvidersQuery(get_called_class());
    }
}
