<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\BackgroundTasksQuery;

/**
 * This is the model class for table "{{%background_tasks}}".
 *
 * @property int $id
 * @property string $key
 * @property int $type 1 - panels; 2 - stores
 * @property string $code
 * @property string $data
 * @property int $status 0 - pending; 1 - in progress; 2 - completed; 3 - error
 * @property string $response
 * @property int $updated_at
 */
class BackgroundTasks extends ActiveRecord
{
    const TYPE_PANELS = 1;
    const TYPE_STORES = 2;

    const STATUS_PENDING = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_ERROR = 3;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.background_tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'type', 'code', 'data'], 'required'],
            [['data', 'response'], 'string'],
            [['updated_at'], 'integer'],
            [['key', 'code'], 'string', 'max' => 300],
            [['type', 'status'], 'integer'],
            [['status'], 'default', 'value' => static::STATUS_PENDING]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'key' => Yii::t('app', 'Key'),
            'type' => Yii::t('app', 'Type'),
            'code' => Yii::t('app', 'Code'),
            'data' => Yii::t('app', 'Data'),
            'status' => Yii::t('app', 'Status'),
            'response' => Yii::t('app', 'Response'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return BackgroundTasksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BackgroundTasksQuery(get_called_class());
    }

    /**
     * Get types
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TYPE_PANELS => Yii::t('app', 'Panels'),
            static::TYPE_PANELS => Yii::t('app', 'Stores'),
        ];
    }

    /**
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_PENDING => Yii::t('app', 'pending'),
            static::STATUS_IN_PROGRESS => Yii::t('app', 'In progress'),
            static::STATUS_COMPLETED => Yii::t('app', 'Completed'),
            static::STATUS_ERROR => Yii::t('app', 'Error'),
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * Set data
     * @param array|mixed $data
     */
    public function setData($data)
    {
        $this->data = json_encode($data);
    }

    /**
     * Get data
     * @return  array|mixed $data
     */
    public function getData()
    {
        return json_decode($this->data, true);
    }

    /**
     * Set response
     * @param array|mixed $response
     */
    public function setResponse($response)
    {
        $this->response = json_encode($response);
    }

    /**
     * Get response
     * @return  array|mixed $data
     */
    public function getResponse()
    {
        return json_decode($this->response, true);
    }

    /**
     * Add task
     * @param int $type
     * @param string $code
     * @param string $key
     * @param mixed $data
     */
    public static function add(int $type, string $code, string $key, $data)
    {
        if (static::findOne([
            'key' => $key
        ])) {
            return;
        }

        $model = (new static([
            'key' => $key,
            'type' => $type,
            'code' => $code,
            'status' => static::STATUS_PENDING
        ]));
        $model->setData($data);
        $model->save();
    }

    /**
     * Set task status
     * @param string $key
     * @param int $status
     * @param mixed $response
     */
    public static function setStatus(string $key, int $status, $response = null)
    {
        static::updateAll([
            'status' => $status,
            'response' => $response ? json_encode($response) : null
        ], '`key` = :key', [
            ':key' => $key
        ]);
    }
}