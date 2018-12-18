<?php

namespace common\models\gateways;

use Yii;
use yii\db\ActiveRecord;
use Exception;
use common\models\gateways\queries\DefaultThemesQuery;
use yii\helpers\ArrayHelper;

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
    private $_gateway;

    public function init()
    {
        parent::init();

        $this->_gateway = Yii::$app->gateway->getInstance();
    }

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

    /**
     * Return Default Themes folder path
     * @return string
     */
    public static function getThemesPath()
    {
        return Yii::getAlias('@gateway') .  '/views/themes/default';
    }

    /**
     * Return default theme path
     * @return string
     * @throws Exception
     */
    public static function getTemplateThemePath()
    {
        $defaultTheme = ArrayHelper::getValue(Yii::$app->params, ['gateway.defaults', 'theme_folder'], null);
        if (!$defaultTheme) {
            throw new Exception('Default theme does not configured!');
        }

        return static::getThemesPath() . '/' . $defaultTheme;
    }

    /**
     * Return theme folder full path
     * @return string
     */
    public function getThemePath()
    {
        return $this->folder ? static::getThemesPath() . '/' . $this->folder : null;
    }

    /**
     * Return is theme active
     * @return bool
     */
    public function isActive()
    {
        return $this->folder === $this->_gateway->theme_folder;
    }

    /**
     * Reset theme file
     * @param $resetFileName
     * @return bool
     */
    public function reset($resetFileName)
    {
        $file = trim(escapeshellarg($resetFileName),'\'');

        if (!$this->isActive()) {
            return false;
        }

        return true;
    }
}