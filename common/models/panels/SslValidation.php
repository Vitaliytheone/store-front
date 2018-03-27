<?php

namespace common\models\panels;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\SslValidationQuery;

/**
 * This is the model class for table "{{%ssl_validation}}".
 *
 * @property integer $id
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
        return '{{%ssl_validation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'file_name', 'content'], 'required'],
            [['pid', 'created_at'], 'integer'],
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
        return $this->hasOne(Project::class, ['id' => 'pid']);
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
