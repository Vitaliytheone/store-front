<?php
namespace admin\models\search;

use common\models\gateways\DefaultThemes;
use common\models\gateways\Sites;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class ThemesSearch
 * @package admin\models\search
 */
class ThemesSearch extends Model
{
    /**
     * @var Sites
     */
    private $_gateway;

    /**
     * @var string
     */
    private $_defaultThemesTable;

    /**
     * @param Sites $gateway
     */
    public function setGateway(Sites $gateway)
    {
        $this->_gateway = $gateway;
        $this->_defaultThemesTable = DefaultThemes::tableName();
    }

    /**
     * Return list of Custom and Default themes
     * @return array
     */
    public function search()
    {
        $themes = (new Query())
           ->select(['id', 'name', 'folder', 'thumbnail'])
            ->from($this->_defaultThemesTable)
            ->orderBy(['position' => SORT_ASC])
            ->all();

        $currentThemeFolder = $this->_gateway->theme_folder;

        // Mark active theme
        foreach ($themes as $idx => &$theme) {
            $active = $theme['folder'] === $currentThemeFolder;
            $theme['active'] = $active;
            $theme['thumbnail'] = ArrayHelper::getValue($theme, 'thumbnail');
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
     * Find theme from Default themes by folder name
     * @param $folderName
     * @return DefaultThemes | false;
     */
    public function searchByFolder($folderName)
    {
        return DefaultThemes::findOne(['folder' => $folderName]);
    }
}