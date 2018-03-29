<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\panels\queries\SuperTasksQuery;

/**
 * This is the model class for table "super_tasks".
 *
 * @property integer $id
 * @property integer $task
 * @property integer $status
 * @property integer $item_id
 * @property string $comment
 * @property integer $created_at
 * @property integer $done_at
 */
class SuperTasks extends ActiveRecord
{
    // Tasks
    const TASK_RESTART_NGINX = 1;
    const TASK_CREATE_NGINX_CONFIG = 2;

    // Task statuses
    const STATUS_PENDING = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_ERROR = 3;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'done_at',
                ],
                'value' => function() {
                    return time();
                },
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.super_tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task', 'status', 'item_id', 'created_at', 'done_at'], 'integer'],
            [['comment', ], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'task' => Yii::t('app', 'Task'),
            'status' => Yii::t('app', 'Status'),
            'item_id' => Yii::t('app', 'Item ID'),
            'comment' => Yii::t('app', 'Comment'),
            'created_at' => Yii::t('app', 'Created'),
            'done_at' => Yii::t('app', 'Done'),
        ];
    }

    /**
     * @inheritdoc
     * @return SuperTasksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SuperTasksQuery(get_called_class());
    }

    /**
     * Set json encoded comment
     * @param $comment array
     */
    public function setComment($comment)
    {
        $this->comment = json_encode($comment);
    }

    /**
     * Get json decoded comment
     * @return array
     */
    public function getComment()
    {
        return json_decode($this->comment, true);
    }
}
