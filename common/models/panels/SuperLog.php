<?php

namespace common\models\panels;

use app\components\behaviors\IpBehavior;
use app\components\behaviors\UserAgentBehavior;
use app\components\traits\UnixTimeFormatTrait;
use Yii;
use common\models\panels\queries\SuperLogQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%super_log}}".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string $action
 * @property string $params
 * @property string $ip
 * @property string $user_agent
 * @property integer $created_at
 *
 * @property SuperAdmin $admin
 */
class SuperLog extends ActiveRecord
{
    const ACTION_AUTH = 1;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%super_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'action', 'params'], 'required'],
            [['id', 'admin_id', 'created_at', 'action'], 'integer'],
            [['ip'], 'string', 'max' => 250],
            [['params', 'user_agent'], 'string', 'max' => 1000],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => SuperAdmin::className(), 'targetAttribute' => ['admin_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'admin_id' => Yii::t('app', 'Admin ID'),
            'action' => Yii::t('app', 'Action'),
            'params' => Yii::t('app', 'Params'),
            'ip' => Yii::t('app', 'Ip'),
            'user_agent' => Yii::t('app', 'User Agent'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(SuperAdmin::className(), ['id' => 'admin_id']);
    }

    /**
     * @inheritdoc
     * @return SuperLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SuperLogQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
            'ip' => [
                'class' => IpBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ]
            ],
            'user_agent' => [
                'class' => UserAgentBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_agent',
                ]
            ],
        ];
    }

    /**
     * Log data
     * @param int $id
     * @param int $action
     * @param array $params
     * @return mixed
     */
    public static function log($id, $action, $params = [])
    {
        $model = new static();
        $model->attributes = [
            'admin_id' => $id,
            'action' => $action,
            'params' => Json::encode($params)
        ];

        return $model->save();
    }
}
