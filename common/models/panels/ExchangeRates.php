<?php

namespace common\models\panels;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\ExchangeRatesQuery;

/**
 * This is the model class for table "{{%exchange_rates}}".
 *
 * @property int $id
 * @property string $source
 * @property string $source_currency
 * @property string $currency
 * @property string $rate
 * @property int $created_at
 */
class ExchangeRates extends ActiveRecord
{
    const SOURCE_CURRENCYLAYER = 'currencylayer';

    const SOURCE_CURRENCY_USD = 'USD';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%exchange_rates}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source', 'source_currency', 'currency', 'rate'], 'required'],
            [['rate'], 'number'],
            [['created_at'], 'integer'],
            [['source'], 'string', 'max' => 255],
            [['source_currency', 'currency'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'source' => Yii::t('app', 'Source'),
            'source_currency' => Yii::t('app', 'Source Currency'),
            'currency' => Yii::t('app', 'Currency'),
            'rate' => Yii::t('app', 'Rate'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ExchangeRatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExchangeRatesQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
                'value' => time(),
            ],
        ];
    }
}