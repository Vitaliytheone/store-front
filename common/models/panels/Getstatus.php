<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "getstatus".
 *
 * @property int $id
 * @property int $date_create
 * @property int $pid
 * @property int $oid
 * @property string $roid
 * @property string $login
 * @property string $passwd
 * @property string $apikey
 * @property string $proxy
 * @property int $res
 * @property string $reid
 * @property string $page_id
 * @property int $count
 * @property int $start_count
 * @property int $statu
 * @property int type
 * @property int updated_at
 * @property string $hash
 */
class Getstatus extends ActiveRecord
{
    const TYPE_PANELS_EXTERNAL = 0;
    const TYPE_PANELS_INTERNAL = 1;
    const TYPE_STORES_EXTERNAL = 2;
    const TYPE_STORES_INTERNAL = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'getstatus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_create', 'pid', 'oid', 'res', 'count', 'start_count', 'status', 'type'], 'integer'],
            [['hash'], 'required'],
            [['roid', 'login', 'passwd', 'apikey', 'proxy', 'reid', 'page_id'], 'string', 'max' => 1000],
            [['hash'], 'string', 'max' => 32],
            ['type', 'in', 'range' => [
                self::TYPE_PANELS_EXTERNAL,
                self::TYPE_PANELS_INTERNAL,
                self::TYPE_STORES_EXTERNAL,
                self::TYPE_STORES_INTERNAL
            ]],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date_create', 'updated_at']
                ],
                'value' => function() {
                    return time();
                }
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date_create' => Yii::t('app', 'Date Create'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'pid' => Yii::t('app', 'Pid'),
            'oid' => Yii::t('app', 'Oid'),
            'roid' => Yii::t('app', 'Roid'),
            'login' => Yii::t('app', 'Login'),
            'passwd' => Yii::t('app', 'Passwd'),
            'apikey' => Yii::t('app', 'Apikey'),
            'proxy' => Yii::t('app', 'Proxy'),
            'res' => Yii::t('app', 'Res'),
            'type' => Yii::t('app', 'Type'),
            'reid' => Yii::t('app', 'Reid'),
            'page_id' => Yii::t('app', 'Page ID'),
            'count' => Yii::t('app', 'Count'),
            'start_count' => Yii::t('app', 'Start Count'),
            'status' => Yii::t('app', 'Status'),
            'hash' => Yii::t('app', 'Hash'),
        ];
    }
}
