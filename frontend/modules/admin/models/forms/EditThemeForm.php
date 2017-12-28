<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use frontend\helpers\CustomFilesHelper;
use frontend\modules\admin\models\search\ThemesSearch;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class EditThemeForm
 * @package frontend\modules\admin\models\forms
 */
class EditThemeForm extends Model
{
    public $file_content;

    private $_theme_model;

    /** @var string Relative to theme folder filepath */
    private $_file;

    /** @var string Path to edited file */
    private $_path_to_file;

    public function formName()
    {
        return 'ThemeForm';
    }

    /**
     * @return array
     */
    public function rules(){
        return [
            ['file_content', 'string']
        ];
    }

    /**
     * Create Form model
     * @param $themeFolderName
     * @param $themeEditFileName
     * @return bool|static
     */
    public static function make($themeFolderName, $themeEditFileName) {

        $themeModel = (new ThemesSearch())->searchByFolder($themeFolderName);

        if (!$themeModel || !$themeModel->isActive()) {
            return false;
        }

        $model = new static();
        $model->_theme_model = $themeModel;
        $model->_file = ltrim(str_replace('../', '', $themeEditFileName), '/');

        if (!$themeEditFileName) {
            return $model;
        }

        /**
         * For Custom theme — save file to theme path
         * For Default theme — save file to custom themes path
         */
        if ($themeModel::THEME_TYPE === 1) {
            $model->_path_to_file = $themeModel->getThemePath() . '/' . $model->_file;
        } else {
            $model->_path_to_file = CustomThemes::getThemesPath() . '/' . $themeModel->folder . '/' . $model->_file;
        }

        return $model;
    }

    /**
     * Return Theme model
     * @return CustomThemes|DefaultThemes
     * @throws Exception
     */
    public function getThemeModel()
    {
        if (!$this->_theme_model) {
            throw new Exception('Theme model does not defined!');
        }

        return $this->_theme_model;
    }

    /**
     * Return relative filepath
     * @return mixed
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Return full path to file depends Theme type
     * @return string
     */
    public function getPathToFile()
    {
        return $this->_path_to_file;
    }

    /**
     * Return theme files tree
     * @return array
     * @throws Exception
     */
    public function getFilesTree()
    {
        $defaultThemePath = $this->getThemeModel()::getDefaultThemePath();
        $filesTree = CustomFilesHelper::dirTree($defaultThemePath, $defaultThemePath, '/^.*\.(twig|css|json|js)$/i');

        return $filesTree;
    }

    /**
     * Return theme file content from file
     * @return string
     * @throws Exception
     */
    public function fetchFileContent()
    {
        if (!$this->getFile()) {
            return null;
        }

        $themeModel = $this->getThemeModel();

        $themeFilePath = $this->getPathToFile();
        $defaultFilePath = $themeModel::getDefaultThemePath() . '/' . $this->getFile();
        $themeRealFilePath = $themeModel->getThemePath() . '/' . $this->getFile();

        /** Custom Theme: Try to find file (1) in theme path and (2) in default folder  */
        if (
            $themeModel::THEME_TYPE === 1 &&
            !file_exists($filePath = $themeFilePath) &&
            !file_exists($filePath = $defaultFilePath)
        ) {
            throw new Exception('Requested theme file does not exist!');
        }

        /** Default Theme: Try to find file (1) in custom folder, (2) in real theme folder, (3) in default folder */
        if (
            $themeModel::THEME_TYPE === 0 &&
            !file_exists($filePath = $themeFilePath) &&
            !file_exists($filePath = $themeRealFilePath) &&
            !file_exists($filePath = $defaultFilePath)
        ) {
            throw new Exception('Requested theme file does not exist!');
        }

        $fileContent = file_get_contents($filePath, false);

        if ($fileContent === false) {
            throw new Exception('Requested file corrupted!');
        }

        $this->file_content = $fileContent;

        return $fileContent;
    }

    /**
     * @return bool
     */
    public function updateThemeFile()
    {
        $pathToFile = $this->getPathToFile();
        $path = dirname($pathToFile);

        if (!file_exists($path)) {
            mkdir($path, 0766, true);
        }

        if (!file_put_contents($pathToFile, $this->file_content)) {
            return false;
        }

        return true;
    }

    /**
     * Return is default theme file changed and it can be reset
     * @return bool
     */
    public function isResetAble()
    {
        $themeModel = $this->getThemeModel();

        $file = $this->getPathToFile();

        return $themeModel::THEME_TYPE === 0 && $file && file_exists($file);
    }

}


