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
                'options'
            ])->all(), 'code');
        }

        return (array)static::$_params;
    }

    /**
     * @param $code
     * @return null
     */
    public static function get($code)
    {
        $parameters = static::getAll();
        if (isset($parameters[$code])) {
            return $parameters[$code]->getOptions();
        }
        return null;
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
