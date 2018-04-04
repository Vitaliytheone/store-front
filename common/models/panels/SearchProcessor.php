<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\SearchProcessorQuery;

/**
 * This is the model class for table "{{%search_processor}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $pid
 * @property integer $result
 * @property string $search
 * @property integer $date
 *
 * @property Project $project
 * @property ProjectAdmin $admin
 */
class SearchProcessor extends ActiveRecord
{
    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.search_processor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'pid', 'result', 'search', 'date'], 'required'],
            [['uid', 'pid', 'result', 'date'], 'integer'],
            [['search'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', 'Uid'),
            'pid' => Yii::t('app', 'Pid'),
            'result' => Yii::t('app', 'Result'),
            'search' => Yii::t('app', 'Search'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @inheritdoc
     * @return SearchProcessorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SearchProcessorQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'pid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(ProjectAdmin::class, ['id' => 'uid']);
    }

    /**
     * Return text-value of result field
     * @return string
     */
    public function getResult()
    {
        return  $this->result ?
            Yii::t('app/superadmin', 'logs.providers.list.result_added') :
            Yii::t('app/superadmin', 'logs.providers.list.result_not_found');
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }
}
