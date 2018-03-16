<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use common\models\panels\queries\PaymentGatewayQuery;

/**
 * This is the model class for table "payment_gateway".
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $pgid
 * @property string $name
 * @property string $minimal
 * @property integer $new_users
 * @property integer $visibility
 * @property string $options
 * @property integer $type
 * @property string $dev_options
 * @property integer $position
 */
class PaymentGateway extends ActiveRecord
{
    const VISIBILITY_ENABLED = 1;
    const VISIBILITY_DISABLED = 0;

    const METHOD_PAYPAL = 1;
    const METHOD_PERFECT_MONEY = 2;
    const METHOD_WEBMONEY = 3;
    const METHOD_TWO_CHECKOUT = 5;
    const METHOD_BITCOIN = 4;
    const METHOD_COINPAYMENTS = 6;

    const METHOD_OTHER = -1;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_gateway';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'pgid', 'name', 'minimal', 'new_users', 'visibility', 'options', 'type'], 'required'],
            [['pid', 'pgid', 'new_users', 'visibility', 'type', 'position'], 'integer'],
            [['minimal'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['options', 'dev_options'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'pgid' => 'Pgid',
            'name' => 'Name',
            'minimal' => 'Minimal',
            'new_users' => 'New Users',
            'visibility' => 'Visibility',
            'options' => 'Options',
            'type' => 'Type',
            'dev_options' => 'Dev Options',
        ];
    }

    /**
     * Get visibility list with labels
     * @return array
     */
    public static function getVisibilityList()
    {
        return [
            static::VISIBILITY_ENABLED => Yii::t('app', 'payment_gateway.visibility.enabled'),
            static::VISIBILITY_DISABLED => Yii::t('app', 'payment_gateway.visibility.disabled')
        ];
    }

    /**
     * Get visibility name
     * @return string
     */
    public function getVisibilityName()
    {
        return static::getVisibilityList()[$this->visibility];
    }

    /**
     * Get methods
     * @return array
     */
    public static function getMethods()
    {
        return [
            static::METHOD_PAYPAL => Yii::t('app', 'payment_gateway.method.paypal'),
            static::METHOD_PERFECT_MONEY => Yii::t('app', 'payment_gateway.method.perfect_money'),
            static::METHOD_WEBMONEY => Yii::t('app', 'payment_gateway.method.webmoney'),
            static::METHOD_TWO_CHECKOUT => Yii::t('app', 'payment_gateway.method.two_checkout'),
            static::METHOD_BITCOIN => Yii::t('app', 'payment_gateway.method.bitcoin'),
            static::METHOD_COINPAYMENTS => Yii::t('app', 'payment_gateway.method.coinpayments'),

            static::METHOD_OTHER => Yii::t('app', 'payment_gateway.method.other'),
        ];
    }

    /**
     * Get method name
     * @return string
     */
    public function getMethodName()
    {
        return static::getMethods()[$this->pgid];
    }

    /**
     * Get method name by method id
     * @param $pgid
     * @return mixed
     */
    public static function getMethodNameById($pgid)
    {
        return static::getMethods()[$pgid];
    }

    /**
     * Get options
     * @return array
     */
    public function getOptionsData()
    {
        return !empty($this->options) ? Json::decode($this->options) : [];
    }

    /**
     * Set options
     * @param array $options
     */
    public function setOptionsData($options)
    {
        $this->options = Json::encode($options);
    }

    /**
     * @inheritdoc
     * @return PaymentGatewayQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentGatewayQuery(get_called_class());
    }
}
