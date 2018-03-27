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
 * @property string $protocol
 * @property integer $type
 * @property integer $created_at
 *
 * @property StoreProviders[] $storeProviders
 */
class Providers extends ActiveRecord
{
    const TYPE_INTERNAL = 0;
    const TYPE_EXTERNAL = 1;

    const PROTOCOL_HTTP = 0;
    const PROTOCOL_HTTPS = 1;

    const API_ACTION_ADD = 'add';
    const API_ACTION_STATUS = 'status';

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
            [['type', 'created_at', 'protocol'], 'integer'],
            [['site'], 'string', 'max' => 45],
            [['protocol'], 'default', 'value' => static::PROTOCOL_HTTP],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'providers.f_id'),
            'site' => Yii::t('admin', 'providers.f_site'),
            'protocol' => Yii::t('admin', 'providers.f_protocol'),
            'type' => Yii::t('admin', 'providers.f_type'),
            'created_at' => Yii::t('admin', 'providers.f_created_at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreProviders()
    {
        return $this->hasMany(StoreProviders::class, ['provider_id' => 'id']);
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
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return ($this->protocol == static::PROTOCOL_HTTPS ? 'https' : 'http') . '://' . $this->site;
    }
}
