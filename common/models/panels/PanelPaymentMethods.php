<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\queries\PanelPaymentMethodsQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%panel_payment_methods}}".
 *
 * @property int $id
 * @property int $panel_id
 * @property int $method_id
 * @property int $currency_id
 * @property string $name
 * @property string $minimal
 * @property string $maximal
 * @property string $options
 * @property int $position
 * @property int $visibility
 * @property int $new_users
 * @property int $take_fee_from_user
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Project $panel
 * @property PaymentMethods $method
 * @property PaymentMethodsCurrency $currency
 */
class PanelPaymentMethods extends ActiveRecord
{
    public const NEW_USERS_ENABLED = 1;
    public const NEW_USERS_DISABLED = 0;

    public const TAKE_FEE_FROM_USER_ENABLED = 1;
    public const TAKE_FEE_FROM_USER_DISABLED = 0;

    use UnixTimeFormatTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%panel_payment_methods}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panel_id', 'method_id', 'name'], 'required'],
            [['panel_id', 'method_id', 'position', 'visibility', 'new_users', 'take_fee_from_user', 'created_at', 'updated_at', 'currency_id'], 'integer'],
            [['minimal', 'maximal'], 'number'],
            ['minimal', 'default', 'value' => 10],
            ['maximal', 'default', 'value' => 0],
            ['visibility', 'default', 'value' => 0],
            ['new_users', 'default', 'value' => 1],
            ['minimal', 'number', 'min' => 1],
            [['options'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['panel_id' => 'id']],
            [['method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::class, 'targetAttribute' => ['method_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethodsCurrency::class, 'targetAttribute' => ['currency_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'panel_id' => Yii::t('app', 'Panel ID'),
            'method_id' => Yii::t('app', 'Method ID'),
            'currency_id' => Yii::t('app', 'Currency ID'),
            'name' => Yii::t('app', 'Name'),
            'minimal' => Yii::t('app', 'Minimal'),
            'maximal' => Yii::t('app', 'Maximal'),
            'options' => Yii::t('app', 'Options'),
            'position' => Yii::t('app', 'Position'),
            'visibility' => Yii::t('app', 'Visibility'),
            'new_users' => Yii::t('app', 'New Users'),
            'take_fee_from_user' => Yii::t('app', 'Take Fee From User'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanel()
    {
        return $this->hasOne(Project::class, ['id' => 'panel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMethod()
    {
        return $this->hasOne(PaymentMethods::class, ['id' => 'method_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(PaymentMethodsCurrency::class, ['id' => 'currency_id']);
    }

    /**
     * {@inheritdoc}
     * @return PanelPaymentMethodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PanelPaymentMethodsQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'positionBehavior' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'position',
                ],
                'value' => function ($event) {
                    return static::find()->andWhere([
                        'panel_id' => $this->panel_id
                    ])->max('position') + 1;
                },
            ],
        ];
    }

    /**
     * @param integer $position
     * @return boolean
     */
    public function changePosition($position)
    {
        $methods = static::find()
            ->select(['id', 'position'])
            ->andWhere([
                'panel_id' => $this->panel_id
            ])
            ->andWhere('id <> ' . $this->id)
            ->orderBy([
                'position' => SORT_ASC
            ])
            ->all();

        $counter = 0;

        $transaction = Yii::$app->db->beginTransaction();
        foreach ($methods as $method) {
            $counter++;

            if ($position == $counter) {
                $counter++;
            }

            if ($counter <> $method->position) {
                $method->position = $counter;
                $method->save(false);
            }
        }

        $this->position = $position;
        $this->save(false);

        $transaction->commit();

        return true;
    }


    /**
     * @param array $options
     */
    public function setOptions($options = [])
    {
        if (($paymentMethodCurrency = PaymentMethodsCurrency::findOne([
            'currency' => $this->panel->getCurrencyCode(),
            'method_id' => $this->method_id
        ])) && !empty($paymentMethodCurrency->settings_form)) {
            $paymentMethodSettings = $paymentMethodCurrency->getSettingsForm();
        } else {
            $paymentMethod = PaymentMethods::findOne([
                'id' => $this->method_id
            ]);

            if (!$paymentMethod) {
                echo 'err';die;
                return;
            }

            $paymentMethodSettings = $paymentMethod->getSettingsForm();
        }

        $cleanOptions = [];
        foreach ($paymentMethodSettings as $method => $details) {
            $cleanOptions[$method] = ArrayHelper::getValue($options, $method);
            if (PaymentMethods::FIELD_TYPE_MULTI_INPUT == $details['type']) {
                $cleanOptions[$method] = (array)$cleanOptions[$method];
            } else {
                $cleanOptions[$method] = (string)$cleanOptions[$method];
            }
        }

        $this->options = json_encode($cleanOptions);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return !empty($this->options) ? @json_decode($this->options, true) : [];
    }
}