<?php

namespace common\models\gateways;

use Yii;
use yii\db\ActiveRecord;
use common\models\gateways\queries\DefaultThemesQuery;

/**
 * This is the model class for table "{{%default_themes}}".
 *
 * @property int $id
 * @property string $name
 * @property string $folder
 * @property int $position
 * @property string $thumbnail
 */
class DefaultThemes extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%default_themes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'folder', 'position'], 'required'],
            [['position'], 'integer'],
            [['name', 'folder', 'thumbnail'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'folder' => Yii::t('app', 'Folder'),
            'position' => Yii::t('app', 'Position'),
            'thumbnail' => Yii::t('app', 'Thumbnail'),
        ];
    }

    /**
     * @inheritdoc
     * @return DefaultThemesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DefaultThemesQuery(get_called_class());
    }
}