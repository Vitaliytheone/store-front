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
    const VISIBILITY_ENABLED = 1;
    const VISIBILITY_DISABLED = 0;

    const CATEGORY_SERVICE = 'service';
    const CATEGORY_PAYMENT = 'payment';

    const CODE_PAYPAL = 'paypal';
    const CODE_PERFECT_MONEY = 'perfect_money';
    const CODE_WEBMONEY = 'webmoney';
    const CODE_TWO_CHECKOUT = '2checkout';
    const CODE_BITCOIN = 'bitcoin';
    const CODE_COINPAYMENTS = 'coinpayments';

    const CODE_WHOISXML = 'whoisxml';
    const CODE_DNSLYTICS = 'dnslytics';
    const CODE_GOGETSSL = 'gogetssl';
    const CODE_AHNAMES = 'ahnames';
    const CODE_OPENSRS = 'opensrs';

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
            [['code', 'options', 'updated_at'], 'required'],
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
            static::$_params = ArrayHelper::index(static::find()->select([
                'code',
                'category',
                'options'
            ])->all(), 'code');
        }

        return (array)static::$_params;
    }

    /**
     * @param $code
     * @param $category
     * @return null
     */
    public static function get($code, $category)
    {
        $parameters = static::getAll();
        if (isset($parameters[$code]) && isset($parameters[$code]->category)) {
            return $parameters[$code]->getOptions();
        }
        return null;
    }

    /**
     * @param $code
     * @return int
     */
    public static function getPaymentPGID($code): int
    {
        $model = static::findOne(['category' => static::CATEGORY_PAYMENT, 'code' => $code]);
        $options = $model->getOptions();

        return $options['pgid'];
    }

    /**
     * @param int $pgid
     * @return array|Params|null
     */
    public static function findByPgid(int $pgid)
    {
        return static::find()
            ->select('*')
            ->andWhere(['or',
                ['like', 'options', '"pgid":'.$pgid],
                ['like', 'options', '"pgid":"'.$pgid.'"']
            ])
            ->one();
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
    public function getOptions(): array
    {
        return json_decode($this->options, true);
    }

    /**
     * @param array $options
     */
    public function setOption($options)
    {
        $this->options = json_encode($options);
    }

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
