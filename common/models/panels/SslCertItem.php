<?php

namespace common\models\panels;

use my\components\ssl\Ssl;
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
 * @property string $allow
 * @property string $generator
 * @property integer $provider
 *
 * @property SslCert[] $sslCerts
 */
class SslCertItem extends ActiveRecord
{
    const GENERATOR_COMODO = 1;
    const GENERATOR_RAPIDSSL = 2;

    const PROVIDER_GOGETSSL = 1;
    const PROVIDER_LETSENCRYPT = 2;

    const PRODUCT_ID_COMODO_POSITIVE = 45;
    const PRODUCT_ID_COMODO_ESSENTIAL= 75;
    const PRODUCT_ID_COMODO_RAPID= 31;
    const PRODUCT_ID_LETSENCRYPT_BASE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.ssl_cert_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'product_id', 'price',], 'required'],
            [['product_id'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 250],
            ['allow', 'string'],
            [['generator', 'provider'],  'integer'],
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
            'allow' => Yii::t('app', 'Allow'),
            'generator' => Yii::t('app', 'Generator'),
            'provider' => Yii::t('app', 'Provider'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSslCerts()
    {
        return $this->hasMany(SslCert::class, ['item_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return SslCertItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SslCertItemQuery(get_called_class());
    }

    /**
     * Set allowed user ids list
     * @param $allowedIds array Array of user ids
     */
    public function setAllow(array $allowedIds)
    {
        $this->allow = json_encode($allowedIds);
    }

    /**
     * Get allowed user ids list
     * @return array|null
     */
    public function getAllow()
    {
        return json_decode($this->allow, true);
    }

    /**
     * Return DCV SSL method by generator
     * @param $isSslRenew bool
     * @return string
     */
    public function getDcvMethod($isSslRenew = false)
    {
        switch ($this->generator) {
            case SslCertItem::GENERATOR_COMODO:
                $dcvMethod = $isSslRenew ? Ssl::DCV_METHOD_HTTPS : Ssl::DCV_METHOD_HTTP;
                break;
            case SslCertItem::GENERATOR_RAPIDSSL:
                $dcvMethod = Ssl::DCV_METHOD_EMAIL;
                break;
            default:
                $dcvMethod = $isSslRenew ? Ssl::DCV_METHOD_HTTPS : Ssl::DCV_METHOD_HTTP;
                break;
        }

        return $dcvMethod;
    }
}
