<?php

namespace common\models\store;

use Yii;

/**
 * This is the model class for table "{{%packages}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $price
 * @property integer $quantity
 * @property integer $link_type
 * @property integer $product_id
 * @property integer $visibility
 * @property integer $best
 * @property integer $mode
 * @property integer $provider_id
 * @property string $provider_service
 * @property integer $position
 *
 * @property Products $product
 * @property Suborders[] $suborders
 */
class Packages extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%packages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'quantity', 'link_type', 'product_id', 'visibility', 'best', 'mode', 'provider_id', 'position'], 'integer'],
            [['price'], 'number'],
            [['name', 'provider_service'], 'string', 'max' => 255],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'price' => Yii::t('app', 'Price'),
            'quantity' => Yii::t('app', 'Quantity'),
            'link_type' => Yii::t('app', 'Link Type'),
            'product_id' => Yii::t('app', 'Product ID'),
            'visibility' => Yii::t('app', 'Visibility'),
            'best' => Yii::t('app', 'Best'),
            'mode' => Yii::t('app', 'Mode'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'provider_service' => Yii::t('app', 'Provider Service'),
            'position' => Yii::t('app', 'Position'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuborders()
    {
        return $this->hasMany(Suborders::className(), ['package_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\queries\PackagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\store\queries\PackagesQuery(get_called_class());
    }
}
