<?php

namespace common\models\stores;

use common\models\common\ThemesInterface;
use common\models\store\CustomThemes;
use common\models\stores\queries\DefaultThemesQuery;
use console\helpers\ConsoleHelper;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "default_themes".
 *
 * @property integer $id
 * @property string $name
 * @property string $folder
 * @property integer $position
 * @property integer $is_customize
 * @property string $thumbnail
 */
class DefaultThemes extends ActiveRecord implements ThemesInterface
{
    private $_store;

    public function init()
    {
        parent::init();

        $this->_store = Yii::$app->store->getInstance();
    }

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
            ['is_customize', 'integer', 'min' => 0, 'max' => 1],
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
            'is_customize' => Yii::t('app', 'Is customize'),
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
     * @inheritdoc
     */
    public static function getThemeType()
    {
        return static::THEME_TYPE_DEFAULT;
    }

    /**
     * Return Default Themes folder path
     * @return string
     */
    public static function getThemesPath()
    {
        return Yii::getAlias('@store') .  '/views/themes/default';
    }

    /**
     * Return path to modified theme default files
     * @return string
     */
    public function getSaveToPath()
    {
        return CustomThemes::getThemesPath() . '/' . $this->folder;
    }

    /**
     * Return default theme path
     * @return string
     * @throws Exception
     */
    public static function getTemplateThemePath()
    {
        $defaultTheme = ArrayHelper::getValue(Yii::$app->params, ['store.defaults', 'theme_folder'], null);
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
        return $this->folder === $this->_store->theme_folder;
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

        $pathToFile = CustomThemes::getThemesPath() . '/' . $this->folder . '/' . $file;

        if (!file_exists($pathToFile) || !is_file($pathToFile)) {
            return false;
        }

        ConsoleHelper::execConsoleCommand('system-sommerce/generate-assets');

        return unlink($pathToFile);
    }

}
