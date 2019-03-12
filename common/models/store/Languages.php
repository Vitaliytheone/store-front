<?php

namespace common\models\store;

use store\modules\admin\helpers\LanguagesHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\Connection;
use \yii\db\ActiveRecord;
use \common\models\store\queries\LanguagesQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{languages}}".
 *
 * @property integer $id
 * @property string $code
 * @property integer $rtl
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $name
 * @property Messages[] $messages
 */
class Languages extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at',
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%languages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rtl', 'created_at', 'updated_at'], 'integer'],
            [['code'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Language code in IETF lang format'),
            'rtl' => Yii::t('app', 'RTL'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return LanguagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LanguagesQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Messages::class, ['lang_code' => 'code']);
    }

    /**
     * Return language name
     * @return string|null
     */
    public function getName()
    {
        return ArrayHelper::getValue(LanguagesHelper::getConfigLanguagesList(true), $this->code);
    }
}
