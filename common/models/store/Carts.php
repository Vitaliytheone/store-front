<?php

namespace common\models\store;

use Yii;

/**
 * This is the model class for table "{{%carts}}".
 *
 * @property integer $id
 * @property string $key
 * @property integer $package_id
 * @property string $link
 * @property integer $created_at
 */
class Carts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%carts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['package_id', 'created_at'], 'integer'],
            [['key'], 'string', 'max' => 64],
            [['link'], 'string', 'max' => 255],
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
            'package_id' => Yii::t('app', 'Package ID'),
            'link' => Yii::t('app', 'Link'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\store\queries\CartsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\store\queries\CartsQuery(get_called_class());
    }
}
