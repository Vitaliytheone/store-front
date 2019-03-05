<?php

namespace common\models\sommerces;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "customers_counters".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $stores
 * @property int $domains
 * @property int $ssl_certs
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customers $customer
 */
class CustomersCounters extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_SOMMERCES . '.customers_counters';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id'], 'required'],
            [['customer_id', 'stores', 'domains', 'ssl_certs', 'created_at', 'updated_at'], 'integer'],
            [['customer_id'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => TimestampBehavior::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'stores' => Yii::t('app', 'Stores'),
            'domains' => Yii::t('app', 'Domains'),
            'ssl_certs' => Yii::t('app', 'Ssl Certs'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'customer_id']);
    }
}
