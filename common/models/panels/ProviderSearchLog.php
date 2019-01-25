<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\ProviderSearchLogQuery;

/**
 * This is the model class for table "{{%search_processor}}".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $panel_id
 * @property integer $result
 * @property string $search
 * @property integer $created_at
 *
 * @property Project $project
 * @property ProjectAdmin $admin
 */
class ProviderSearchLog extends ActiveRecord
{
    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.provider_search_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'panel_id', 'result', 'search', 'created_at'], 'required'],
            [['admin_id', 'panel_id', 'result', 'created_at'], 'integer'],
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
            'admin_id' => Yii::t('app', 'Admin id'),
            'panel_id' => Yii::t('app', 'Panel id'),
            'result' => Yii::t('app', 'Result'),
            'search' => Yii::t('app', 'Search'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
    }

    /**
     * @inheritdoc
     * @return ProviderSearchLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProviderSearchLogQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'panel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(ProjectAdmin::class, ['id' => 'admin_id']);
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }
}
