<?php
namespace common\models\store;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\BlocksQuery;

/**
 * This is the model class for table "{{%blocks}}".
 *
 * @property integer $id
 * @property string $code
 * @property string $content
 * @property integer $updated_at
 */
class Blocks extends ActiveRecord
{
    const CODE_SLIDER = 'slider';
    const CODE_REVIEW = 'review';
    const CODE_PROCESS = 'process';
    const CODE_FEATURES = 'features';

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%blocks}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'content'], 'required'],
            [['updated_at'], 'integer'],
            [['content'], 'string'],
            [['code'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'content' => Yii::t('app', 'Content'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return BlocksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BlocksQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
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
}