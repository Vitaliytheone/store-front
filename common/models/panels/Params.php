<?php
namespace common\models\panels;

use Yii;
use common\models\panels\queries\ParamsQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "params".
 *
 * @property int $id
 * @property string $category
 * @property string $code
 * @property string $options
 * @property int $updated_at
 * @property int $position
 */
class Params extends ActiveRecord
{
    public const VISIBILITY_ENABLED = 1;
    public const VISIBILITY_DISABLED = 0;

    public const CATEGORY_SERVICE = 'service';
    public const CATEGORY_PAYMENT = 'payment';

    public const CODE_OTHER = 'other';
    public const CODE_PAYPAL = 'paypal';
    public const CODE_PERFECT_MONEY = 'perfect_money';
    public const CODE_WEBMONEY = 'webmoney';
    public const CODE_TWO_CHECKOUT = '2checkout';
    public const CODE_BITCOIN = 'bitcoin';
    public const CODE_COINPAYMENTS = 'coinpayments';

    public const CODE_WHOISXML = 'whoisxml';
    public const CODE_DNSLYTICS = 'dnslytics';
    public const CODE_GOGETSSL = 'gogetssl';
    public const CODE_AHNAMES = 'ahnames';
    public const CODE_OPENSRS = 'opensrs';

    public const CODE_LETSENCRYPT = 'letsencrypt';

    public const CODE_WHOISXMLAPI = 'whoisxmlapi';

    /**
     * After this time after payment refund paypal transaction impossible
     */
    public const PAYPAL_REFUND_EXPIRED_AFTER = 180 * 24 * 60 * 60;

    /**
     * @var static[]
     */
    protected static $_params;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'options'], 'required'],
            [['options'], 'string'],
            [['updated_at', 'position'], 'integer'],
            [['code', 'category'], 'string', 'max' => 64],
            [['code', 'category'], 'unique', 'targetAttribute' => ['code', 'category']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'options' => 'Options',
            'updated_at' => 'Updated At',
            'position' => 'Position',
            'category' => 'Category',
        ];
    }

    /**
     * @return array
     */
    public static function getPayments(): array
    {
        return [
            static::CODE_OTHER => Yii::t('app', 'payment_gateway.method.other'),
            static::CODE_PAYPAL => Yii::t('app', 'payment_gateway.method.paypal'),
            static::CODE_PERFECT_MONEY => Yii::t('app', 'payment_gateway.method.perfect_money'),
            static::CODE_WEBMONEY => Yii::t('app', 'payment_gateway.method.webmoney'),
            static::CODE_TWO_CHECKOUT => Yii::t('app', 'payment_gateway.method.two_checkout'),
            static::CODE_BITCOIN => Yii::t('app', 'payment_gateway.method.bitcoin'),
            static::CODE_COINPAYMENTS => Yii::t('app', 'payment_gateway.method.coinpayments'),
        ];
    }

    public static function getPaymentName($code)
    {
        return ArrayHelper::getValue(static::getPayments(), $code, 'All');
    }

    /**
     * {@inheritdoc}
     * @return ParamsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ParamsQuery(get_called_class());
    }

    /**
     * Get all parameters
     * @return array
     */
    public static function getAll():array
    {
        if (null === static::$_params) {
            $params = static::find()->select([
                'code',
                'category',
                'options'
            ])->asArray()->orderBy([
                'position' => SORT_ASC
            ])->all();

            static::$_params = ArrayHelper::index(array_map(function($value) {
                $value['options'] = json_decode($value['options'], true);
                return $value;
            }, $params), 'code', 'category');
        }

        return (array)static::$_params;
    }

    /**
     * Get parameter
     * @param string $category
     * @param string $code
     * @param string $optionKey
     * @return null|array
     */
    public static function get($category, $code, $optionKey = null)
    {
        $params = ArrayHelper::getValue(static::getAll(), [$category, $code, 'options']);

        if (!$optionKey) {
            return $params;
        }

        return ArrayHelper::getValue($params, $optionKey);
    }

    /**
     * @return array
     */
    public static function indexByPgid(): array
    {
        $payments = Params::find()->where(['category' => Params::CATEGORY_PAYMENT])->all();

        $paymentsList = [];
        foreach ($payments as $key => $method) {
            $options = $method->getOptions();
            $paymentsList[$options['pgid']] = $options['name'];
        }

        return $paymentsList;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return json_decode($this->options, true);
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = json_encode($options);
    }

    /**
     * Return option by key
     * @param $optionKey string
     * @return null|mixed
     */
    public function getOption(string $optionKey)
    {
        $options = $this->getOptions();
        $options = is_array($options) ? $options : [];

        return ArrayHelper::getValue($options, $optionKey, null);
    }

    /**
     * Set option value by key
     * @param $optionKey string
     * @param $optionValue
     * @return array
     */
    public function setOption(string $optionKey, $optionValue)
    {
        $options = $this->getOptions();
        $options = is_array($options) ? $options : [];

        $this->setOptions(array_merge($options, [$optionKey => $optionValue]));
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * Get visibility list with labels
     * @return array
     */
    public static function getVisibilityList(): array
    {
        return [
            static::VISIBILITY_ENABLED => Yii::t('app', 'payment_gateway.visibility.enabled'),
            static::VISIBILITY_DISABLED => Yii::t('app', 'payment_gateway.visibility.disabled')
        ];
    }
}
