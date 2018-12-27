<?php

namespace common\models\stores;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\stores\queries\PaymentMethodsCurrencyQuery;

/**
 * This is the model class for table "{{%payment_methods_currency}}".
 *
 * @property int $id
 * @property int $method_id
 * @property string $currency
 * @property int $position
 * @property string $settings_form
 * @property string $settings_form_description
 * @property int $hidden
 * @property int $created_at
 * @property int $updated_at
 */
class PaymentMethodsCurrency extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_methods_currency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['method_id', 'position', 'hidden', 'created_at', 'updated_at'], 'integer'],
            [['settings_form', 'settings_form_description'], 'string'],
            [['currency'], 'string', 'max' => 3],
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
            'method_id' => Yii::t('app', 'Method ID'),
            'currency' => Yii::t('app', 'Currency'),
            'position' => Yii::t('app', 'Position'),
            'settings_form' => Yii::t('app', 'Settings Form'),
            'settings_form_description' => Yii::t('app', 'Settings Form Description'),
            'hidden' => Yii::t('app', 'Hidden'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     * @return PaymentMethodsCurrencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentMethodsCurrencyQuery(get_called_class());
    }

    /**
     * Set settings form
     * @param $options
     */
    public function setSettingsForm($options)
    {
        $this->settings_form = json_encode($options);
    }

    /**
     * Get settings form
     * @return array
     */
    public function getSettingsForm(): array
    {
        return !empty($this->settings_form) ? json_decode($this->settings_form, true) : [];
    }

    /**
     * Set settings form description
     * @param $description
     */
    public function setSettingsFormDescription($description)
    {
        $this->settings_form_description = json_encode($description);
    }

    /**
     * Get settings form description
     * @return array
     */
    public function getSettingsFormDescription(): array
    {
        return !empty($this->settings_form_description) ? json_decode($this->settings_form_description, true) : [];
    }

    /**
     * Get currency support by current store
     * @return array
     */
    public static function getSupportCurrency(): array
    {
        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        $currencies = self::find()
            ->filterWhere(['hidden' => 0])
            ->andFilterWhere(['currency' => $store->currency])
            ->indexBy('id')
            ->asArray()
            ->all();

        $storePaymentMethods = StorePaymentMethods::find()
            ->where(['store_id' => $store->id])
            ->indexBy('currency_id')
            ->asArray()
            ->all();

        $result = [];
        foreach ($currencies as $id => $method) {
            if (isset($storePaymentMethods[$id])) {
                continue;
            }

            $result[$id] = StorePaymentMethods::getNameById($method['method_id']);
        }

        return $result;
    }

    public static function getMethodsIdByCurrency(string $currency): array
    {
        // TODO get array with methods id which has $currency
    }
}
