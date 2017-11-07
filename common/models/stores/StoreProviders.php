<?php

namespace common\models\stores;

use Yii;
use yii\db\ActiveRecord;
use common\models\stores\queries\StoreProvidersQuery;

/**
 * This is the model class for table "{{%store_providers}}".
 *
 * @property integer $id
 * @property integer $provider_id
 * @property integer $store_id
 * @property string $apikey
 *
 * @property Providers $provider
 * @property Stores $store
 */
class StoreProviders extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%store_providers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider_id', 'store_id'], 'integer'],
            [['apikey'], 'string', 'max' => 255],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Providers::className(), 'targetAttribute' => ['provider_id' => 'id']],
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
            'provider_id' => Yii::t('app', 'Provider ID'),
            'store_id' => Yii::t('app', 'Store ID'),
            'apikey' => Yii::t('app', 'Apikey'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Providers::className(), ['id' => 'provider_id']);
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
     * @return StoreProvidersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoreProvidersQuery(get_called_class());
    }
}
