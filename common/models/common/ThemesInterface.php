<?php
namespace common\models\common;

/**
 * Interface ThemesInterface
 * @package common\models\common
 */
interface ThemesInterface
{
    const THEME_TYPE_DEFAULT = 0;
    const THEME_TYPE_CUSTOM = 1;

    /**
     * Return theme type
     * @return integer
     */
    public static function getThemeType();

    /**
     * Return path to source template
     * @return mixed
     */
    public static function getTemplateThemePath();

    /**
     * Return path to themes
     * @return mixed
     */
    public static function getThemesPath();

    /**
     * Return theme files save path
     * @return mixed
     */
    public function getSaveToPath();

}