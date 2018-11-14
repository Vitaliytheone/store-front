<?php

namespace common\models\panel;

use Yii;
use yii\db\ActiveRecord;
use common\models\panel\queries\BonusesQuery;

/**
 * This is the model class for table "{{%bonuses}}".
 *
 * @property int $id
 * @property int $pgid
 * @property string $amount
 * @property string $deposit_from
 * @property string $deposit_to
 * @property int $status 0 - disabled, 1 - enabled
 * @property int $created_at
 * @property int $updated_at
 */
class Bonuses extends ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bonuses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pgid', 'amount', 'deposit_from', 'deposit_to', 'status', 'created_at', 'updated_at'], 'required'],
            [['pgid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'deposit_from', 'deposit_to'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pgid' => Yii::t('app', 'Pgid'),
            'amount' => Yii::t('app', 'Amount'),
            'deposit_from' => Yii::t('app', 'Deposit From'),
            'deposit_to' => Yii::t('app', 'Deposit To'),
            'status' => Yii::t('app', '0 - disabled, 1 - enabled'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return BonusesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BonusesQuery(get_called_class());
    }


    /**
     * Return bonus statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
          self::STATUS_ENABLED => Yii::t('admin/settings', 'bonuses.list.status.enabled'),
          self::STATUS_DISABLED =>  Yii::t('admin/settings', 'bonuses.list.status.disabled')
        ];
    }
}
