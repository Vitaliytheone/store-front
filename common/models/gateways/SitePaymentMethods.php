<?php

namespace common\models\gateways;

use Yii;
use yii\db\ActiveRecord;
use common\models\gateways\queries\SitePaymentMethodsQuery;

/**
 * This is the model class for table "{{%site_payment_methods}}".
 *
 * @property int $id
 * @property int $site_id
 * @property int $method_id
 * @property string $options
 * @property int $visibility
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Sites $site
 * @property PaymentMethods $method
 */
class SitePaymentMethods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_payment_methods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'site_id', 'method_id', 'options', 'created_at'], 'required'],
            [['id', 'site_id', 'method_id', 'created_at', 'updated_at'], 'integer'],
            [['options'], 'string'],
            [['visibility'], 'string', 'max' => 1],
            [['site_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sites::class, 'targetAttribute' => ['site_id' => 'id']],
            [['method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::class, 'targetAttribute' => ['method_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'site_id' => Yii::t('app', 'Site ID'),
            'method_id' => Yii::t('app', 'Method ID'),
            'options' => Yii::t('app', 'Options'),
            'visibility' => Yii::t('app', 'Visibility'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Sites::class, ['id' => 'site_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMethod()
    {
        return $this->hasOne(PaymentMethods::class, ['id' => 'method_id']);
    }

    /**
     * @inheritdoc
     * @return SitePaymentMethodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SitePaymentMethodsQuery(get_called_class());
    }
}