<?php

namespace common\models\stores;

use Yii;

/**
 * This is the model class for table "{{%store_domains}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property string $domain
 * @property integer $type
 *
 * @property Stores $store
 */
class StoreDomains extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%store_domains}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'type'], 'integer'],
            [['domain'], 'string', 'max' => 255],
            [['domain'], 'unique'],
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
            'domain' => Yii::t('app', 'Domain'),
            'type' => Yii::t('app', '0 - standart, 1 - additional, 2 - subdomain'),
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
     * @return \common\models\stores\queries\StoreDomainsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\stores\queries\StoreDomainsQuery(get_called_class());
    }
}
