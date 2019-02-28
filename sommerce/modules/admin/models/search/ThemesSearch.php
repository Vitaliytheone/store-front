<?php

namespace sommerce\modules\admin\models\search;

use common\models\sommerce\CustomThemes;
use common\models\sommerces\DefaultThemes;
use common\models\sommerces\Stores;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class ThemesSearch
 * @package sommerce\modules\admin\models\search
 */
class ThemesSearch extends Model
{
    /**
     * @var Stores
     */
    private $_store;

    /**
     * @var string
     */
    private $_customThemesTable;

    /**
     * @var string
     */
    private $_defaultThemesTable;

    /**
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
        $storeDb = $this->_store->db_name;
        $this->_defaultThemesTable = DefaultThemes::tableName();
        $this->_customThemesTable = $storeDb . "." . CustomThemes::tableName();
    }

    /**
     * Return list of Custom and Default themes
     * @return array
     */
    public function search()
    {
        $defaultThemes = (new Query())
           ->select(['id', 'name', 'folder', 'thumbnail', 'is_customize'])
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
            $theme['is_customize'] = (boolean)ArrayHelper::getValue($theme, 'is_customize');
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