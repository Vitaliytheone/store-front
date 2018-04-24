<?php

namespace common\models\panels;

use common\models\common\ProjectInterface;
use common\models\stores\Stores;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\SslValidationQuery;

/**
 * This is the model class for table "{{%ssl_validation}}".
 *
 * @property integer $id
 * @property integer $ptype
 * @property integer $pid
 * @property string $file_name
 * @property string $content
 * @property integer $created_at
 *
 * @property Project $p
 */
class SslValidation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.ssl_validation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'ptype', 'file_name', 'content'], 'required'],
            [['pid', 'ptype', 'created_at'], 'integer'],
            [['file_name'], 'string', 'max' => 250],
            [['content'], 'string', 'max' => 1000],
            [['pid'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['pid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ptype' => Yii::t('app', 'Project type'),
            'pid' => Yii::t('app', 'Pid'),
            'file_name' => Yii::t('app', 'File Name'),
            'content' => Yii::t('app', 'Content'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getP()
    {
        switch ($this->ptype) {
            case ProjectInterface::PROJECT_TYPE_PANEL:
                return $this->hasOne(Project::class, ['id' => 'pid']);
                break;
            case ProjectInterface::PROJECT_TYPE_STORE:
                return $this->hasOne(Stores::class, ['id' => 'pid']);
                break;
            default:
                return $this->hasOne(Project::class, ['id' => 'pid']);
                break;
        }
    }

    /**
     * @inheritdoc
     * @return SslValidationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SslValidationQuery(get_called_class());
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
}
