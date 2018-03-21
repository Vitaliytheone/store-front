<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use common\models\panels\queries\SslCertItemQuery;

/**
 * This is the model class for table "{{%ssl_cert_item}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $product_id
 * @property string $price
 *
 * @property SslCert[] $sslCerts
 */
class SslCertItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ssl_cert_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'product_id', 'price'], 'required'],
            [['product_id'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 250],
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
            'product_id' => Yii::t('app', 'Product ID'),
            'price' => Yii::t('app', 'Price'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSslCerts()
    {
        return $this->hasMany(SslCert::className(), ['item_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return SslCertItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SslCertItemQuery(get_called_class());
    }
}
