<?php

namespace common\models\sommerces;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerces\queries\LinkValidationsQuery;

/**
 * This is the model class for table "{{%link_validations}}".
 *
 * @property int $id
 * @property string $link
 * @property int $status 0 - invalid; 1 - valid
 * @property int $link_type
 * @property int $store_id
 * @property int $created_at
 *
 * @property Stores $store
 */
class LinkValidations extends ActiveRecord
{
    const STATUS_INVALID = 0;
    const STATUS_VALID = 1;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_STORES . '.link_validations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['link', 'link_type', 'store_id'], 'required'],
            [['link_type', 'store_id', 'created_at'], 'integer'],
            [['link'], 'string', 'max' => 1000],
            [['status'], 'string', 'max' => 1],
            [['status'], 'default', 'value' => static::STATUS_INVALID],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'link' => Yii::t('app', 'Link'),
            'status' => Yii::t('app', '0 - invalid; 1 - valid'),
            'link_type' => Yii::t('app', 'Link Type'),
            'store_id' => Yii::t('app', 'Store ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::class, ['id' => 'store_id']);
    }

    /**
     * @inheritdoc
     * @return LinkValidationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LinkValidationsQuery(get_called_class());
    }

    /**
     * @return array
     */
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
     * @param string $link
     * @param int $linkType
     * @param int $storeId
     * @param null|int $status
     */
    public static function add(string $link, int $linkType, int $storeId, int $status = null): void
    {
        $model = new static();
        $model->link = $link;
        $model->link_type = $linkType;
        $model->store_id = $storeId;
        $model->status = $status ? $status : static::STATUS_INVALID;
        $model->save(false);
    }
}