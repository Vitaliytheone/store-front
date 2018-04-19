<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\ThirdPartyLogQuery;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%third_party_log}}".
 *
 * @property integer $id
 * @property integer $item
 * @property integer $item_id
 * @property string $code
 * @property string $details
 * @property integer $created_at
 */
class ThirdPartyLog extends ActiveRecord
{
    const ITEM_BUY_PANEL = 1;
    const ITEM_PROLONGATION_PANEL = 2;
    const ITEM_BUY_DOMAIN = 3;
    const ITEM_PROLONGATION_DOMAIN = 4;
    const ITEM_BUY_SSL = 5;
    const ITEM_PROLONGATION_SSL = 6;
    const ITEM_ORDER = 7;
    const ITEM_BUY_STORE = 8;
    const ITEM_PROLONGATION_STORE = 9;
    const ITEM_REFUND_PAYPAL_PAYMENT = 10;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.third_party_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item', 'item_id'], 'required'],
            [['item', 'item_id', 'created_at'], 'integer'],
            [['details', 'code'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'item' => Yii::t('app', 'Item'),
            'item_id' => Yii::t('app', 'Item ID'),
            'code' => Yii::t('app', 'Code'),
            'details' => Yii::t('app', 'Details'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ThirdPartyLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ThirdPartyLogQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ]
        ];
    }

    /**
     * Set details
     * @param array $details
     */
    public function setDetails($details)
    {
        $this->details = Json::encode($details);
    }

    /**
     * Get details
     * @return array $details
     */
    public function getDetails()
    {
         return Json::decode($this->details);
    }

    /**
     * Save log data
     * @param int $item
     * @param int $item_id
     * @param mixed $details
     * @param string $code
     * @return mixed
     */
    public static function log($item, $item_id, $details, $code = null)
    {
        $model = new static();
        $model->item = $item;
        $model->item_id = $item_id;
        $model->code = $code;
        $model->setDetails($details);

        return $model->save(false);
    }
}
