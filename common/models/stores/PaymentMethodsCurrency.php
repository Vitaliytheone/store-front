<?php

namespace common\models\stores;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\stores\queries\PaymentMethodsCurrencyQuery;
use yii\helpers\ArrayHelper;

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
    public const NOT_HIDDEN = 0;
    public const HIDDEN = 1;

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
        $this->settings_form_description = $description;
    }

    /**
     * Get settings form description
     * @return string
     */
    public function getSettingsFormDescription(): string
    {
        return !empty($this->settings_form_description) ? $this->settings_form_description : '';
    }

    /**
     * Get only not yet added PayMethods for store support by current currency
     * @param Stores $store
     * @param \common\models\stores\queries\StorePaymentMethodsQuery $query
     * @return array
     */
    public static function getSupportPaymentMethods(Stores $store, $query = null): array
    {
        $currencies = self::find()
            ->active()
            ->andFilterWhere(['currency' => $store->currency])
            ->indexBy('id')
            ->asArray()
            ->all();

        if ($query === null) {
            $storePaymentMethods = StorePaymentMethods::find()
                ->where(['store_id' => $store->id])
                ->indexBy('currency_id')
                ->orderBy('name')
                ->asArray()
                ->all();
        } else {
            $storePaymentMethods = $query;
        }

        $storePaymentMethods = ArrayHelper::toArray($storePaymentMethods);
        $storePaymentMethods = ArrayHelper::index($storePaymentMethods, 'currency_id');

        $methodsIds = array_column($storePaymentMethods, 'method_id');

        $result = [];
        $methodsNames = StorePaymentMethods::getNames();
        foreach ($currencies as $id => $method) {
            if (isset($storePaymentMethods[$id])) {
                continue;
            }

            if (in_array($method['method_id'], $methodsIds)) {
                continue;
            }

            $result[$id] = $methodsNames[$method['method_id']];
        }

        asort($result);
        return $result;
    }

    /**
     * Get all ids of PayMethods support by current Store currency
     * @param Stores $store
     * @return array
     */
    public static function getAllSupportPaymentMethods(Stores $store): array
    {
        $currencies = self::find()
            ->active()
            ->andFilterWhere(['currency' => $store->currency])
            ->select('method_id')
            ->indexBy('method_id')
            ->distinct()
            ->asArray()
            ->all();

        $currencies = ArrayHelper::map($currencies, 'method_id', 'method_id');
        $currencies = array_fill_keys($currencies, '0');
        return $currencies;
    }

    /**
     * Get currency ids of passed currency
     * @param string $currency
     * @param string $indexBy
     * @return array
     */
    public static function getMethodsByCurrency(string $currency, string $indexBy = 'id'): array
    {
        return static::find()
            ->where(['currency' => $currency])
            ->indexBy($indexBy)
            ->asArray()
            ->all();
    }
}