<?php

namespace common\models\sommerces;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerces\queries\StoreDomainsQuery;

/**
 * This is the model class for table "{{%store_domains}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property string $domain
 * @property integer $type
 * @property integer $ssl
 * @property integer $updated_at
 *
 * @property Stores $store
 */
class StoreDomains extends ActiveRecord
{
    const DOMAIN_TYPE_SOMMERCE = 0;
    const DOMAIN_TYPE_DEFAULT = 1;
    const DOMAIN_TYPE_ADDITIONAL = 2;
    const DOMAIN_TYPE_SUBDOMAIN = 3;

    const SSL_OFF = 0;
    const SSL_ON = 1;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_SOMMERCES . '.store_domains';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'type', 'ssl', 'updated_at'], 'integer'],
            [['domain'], 'string', 'max' => 255],
            [['domain'], 'unique'],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
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
            'type' => Yii::t('app', 'Type'),
            'ssl' => Yii::t('app', 'SSL'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::class, ['id' => 'store_id']);
    }

    /**
     * @inheritdoc
     * @return StoreDomainsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoreDomainsQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }
}
