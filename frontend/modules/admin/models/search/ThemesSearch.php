<?php

namespace frontend\modules\admin\models\search;

use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ThemesSearch extends Model
{

    private $_store;
    private $_customThemesTable;
    private $_defaultThemesTable;

    public function init()
    {
        $this->_store = Yii::$app->store->getInstance();

        $storeDb = $this->_store->db_name;

        $this->_defaultThemesTable = DefaultThemes::tableName();
        $this->_customThemesTable = $storeDb . "." . CustomThemes::tableName();

        parent::init();
    }

    /**
     * Return list of Custom and Default themes
     * @return array
     */
    public function search()
    {
        $defaultThemes = (new Query())
            ->select(['id', 'name', 'folder', 'thumbnail'])
            ->from($this->_defaultThemesTable)
            ->orderBy(['position' => SORT_ASC])
            ->all();

        $customThemes = (new Query())
            ->select(['id', 'name', 'folder'])
            ->from($this->_customThemesTable)
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $themes = array_merge($defaultThemes, $customThemes);

        $currentThemeFolder = $this->_store->theme_folder;

        // Mark active theme
        foreach ($themes as $idx => &$theme) {
            $active = $theme['folder'] === $currentThemeFolder;
            $theme['active'] = $active;
            $theme['thumbnail'] = ArrayHelper::getValue($theme, 'thumbnail', CustomThemes::THEME_THUMBNAIL_URL);
        }

        $activeItemIdx = array_search(true, array_column($themes, 'active'));

        // Move active theme to first position
        if ($activeItemIdx) {
            $activeItem = $themes[$activeItemIdx];
            unset($themes[$activeItemIdx]);
            array_unshift($themes, $activeItem);
        }

        return $themes;
    }

    /**
     * Find theme from Custom and Default themes by folder name
     * @param $folderName
     * @return CustomThemes | DefaultThemes | false;
     */
    public function searchByFolder($folderName)
    {
        $isDefaultTheme = false === strpos($folderName, CustomThemes::THEME_PREFIX);

        return $isDefaultTheme ? DefaultThemes::findOne(['folder' => $folderName]) : CustomThemes::findOne(['folder' => $folderName]);
    }
}