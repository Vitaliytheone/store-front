<?php

namespace common\models\panels;

use common\models\common\ProjectInterface;
use common\models\gateways\Sites;
use common\models\stores\Stores;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\LogsQuery;

/**
 * This is the model class for table "{{%logs}}".
 *
 * @property integer $id
 * @property integer $project_type
 * @property integer $panel_id
 * @property string $data
 * @property integer $type
 * @property integer $created_at
 */
class Logs extends ActiveRecord
{
    const TYPE_TERMINATED = 1;
    const TYPE_RESTORED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_type', 'panel_id', 'type'], 'required'],
            [['project_type', 'panel_id', 'type', 'created_at'], 'integer'],
            [['data'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'project_type' => Yii::t('app', 'Project type'),
            'panel_id' => Yii::t('app', 'Panel ID'),
            'data' => Yii::t('app', 'Data'),
            'type' => Yii::t('app', 'Type'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return LogsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LogsQuery(get_called_class());
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
            ],
        ];
    }

    /**
     * Get types
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TYPE_TERMINATED => Yii::t('app', 'logs.type.terminated'),
            static::TYPE_RESTORED => Yii::t('app', 'logs.type.restored')
        ];
    }

    /**
     * Get type name
     * @return string
     */
    public function getTypeName()
    {
        return static::getTypes()[$this->type];
    }

    /**
     * Log
     * @param Project|Stores|Sites $project
     * @param int $type
     * @param string $data
     * @return bool
     * @throws Exception
     */
    public static function log($project, $type, $data = '')
    {
        if (!$project instanceof ProjectInterface) {
            throw new Exception('Type mismatch exception! The $project must implement an ProjectInterface interface!');
        }

        $model = new static();
        $model->project_type = $project::getProjectType();
        $model->panel_id = $project->id;
        $model->type = $type;
        $model->data = $data;

        return $model->save();
    }
}
