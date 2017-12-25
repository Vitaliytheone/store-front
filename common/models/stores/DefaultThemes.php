<?php

namespace common\models\stores;

use Yii;
use yii\db\ActiveRecord;
use common\models\stores\queries\DefaultThemesQuery;

/**
 * This is the model class for table "default_themes".
 *
 * @property integer $id
 * @property string $name
 * @property string $folder
 * @property integer $position
 * @property string $thumbnail
 */
class DefaultThemes extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'default_themes';
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

    /**
     * Return Default Themes folder path
     * @return string
     */
    public static function getThemesPath()
    {
        return Yii::getAlias('@frontend') .  '/views/themes/default';
    }

    /**
     * Return theme folder full path
     * @return string
     */
    public function getThemePath()
    {
        return $this->folder ? static::getThemesPath() . '/' . $this->folder : null;
    }

}
