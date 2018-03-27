<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\DomainZonesQuery;

/**
 * This is the model class for table "{{%domain_zones}}".
 *
 * @property integer $id
 * @property string $zone
 * @property string $price_register
 * @property string $price_renewal
 * @property string $price_transfer
 *
 * @property Domains[] $domains
 */
class DomainZones extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%domain_zones}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zone', 'price_register', 'price_renewal', 'price_transfer'], 'required'],
            [['price_register', 'price_renewal', 'price_transfer'], 'number'],
            [['zone'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'zone' => Yii::t('app', 'Zone'),
            'price_register' => Yii::t('app', 'Price Register'),
            'price_renewal' => Yii::t('app', 'Price Renewal'),
            'price_transfer' => Yii::t('app', 'Price Transfer'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomains()
    {
        return $this->hasMany(Domains::class, ['zone_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return DomainZonesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DomainZonesQuery(get_called_class());
    }
}
