<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use common\models\stores\StoreAdminAuth;
use console\helpers\ConsoleHelper;
use sommerce\modules\admin\models\search\ThemesSearch;
use yii\base\Exception;
use yii\base\Model;
use yii\web\User;

/**
 * Class EditThemeForm
 * @package sommerce\modules\admin\models\forms
 */
class EditThemeForm extends Model
{
    /**
     * Theme allowed folders/files structure
     * @var array
     */
    private $_filesTree = [
        'Layouts' => [
            'layout.twig',
        ],
        'Templates' => [
            'index.twig',
            'product.twig',
            'order.twig',
            'page.twig',
            'cart.twig',
            '404.twig',
            'contact.twig',
            'payment_result.twig',
        ],
        'Snippets' => [
            'slider.twig',
            'features.twig',
            'reviews.twig',
            'process.twig',
        ],
        'JS' => [],
        'CSS' => [],
//        'Config' => [
//            'settings.json'
//        ],
    ];

    /** @var   */
    public $file_content;

    /** @var  */
    private $_theme_model;

    /** @var string Relative to theme folder filepath */
    private $_file;

    /** @var string Path to edited file */
    private $_path_to_file;

    /** @var  User */
    protected $_user;

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
     * Set current user
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }


    /**
     * Get current user
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Create Form model
     * @param $themeFolderName
     * @param $themeEditFileName
     * @return bool|static
     */
    public static function make($themeFolderName, $themeEditFileName) {

        $themeModel = (new ThemesSearch())->searchByFolder($themeFolderName);
        $fileName = ltrim(str_replace('../', '', $themeEditFileName), '/');


        if (!$themeModel || !$themeModel->isActive()) {
            return false;
        }

        $model = new static();
        $model->_theme_model = $themeModel;

        $model->setFilesTree();

        if (!$themeEditFileName) {
            return $model;
        }


        /** Check is filename is allowed */
        if (strpos(json_encode($model->getFilesTree()), $fileName) === false) {
            return false;
        }

        $model->_file = $fileName;

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
     * Init files tree
     */
    public function setFilesTree()
    {
        // $defaultThemePath = $this->getThemeModel()::getDefaultThemePath();
        // $filesTree = CustomFilesHelper::dirTree($defaultThemePath, $defaultThemePath, '/^.*\.(css|js)$/i');

        $themePath = $this->getThemeModel()->getThemePath();

        $themeFiles = scandir($themePath);

        foreach ($themeFiles as $file) {

            if(!is_file($themePath . DIRECTORY_SEPARATOR . $file)) {
                continue;
            }
            if (strcasecmp(pathinfo($file, PATHINFO_EXTENSION), 'js') === 0) {
                $this->_filesTree['JS'][] = $file;
            }
            if (strcasecmp(pathinfo($file, PATHINFO_EXTENSION), 'css') === 0) {
                $this->_filesTree['CSS'][] = $file;
            }
        }

        foreach ($this->_filesTree as $key=>&$folder)
        {
            sort($folder);
        }

        return $this->_filesTree;
    }

    /**
     * Return theme files tree
     * @return array
     * @throws Exception
     */
    public function getFilesTree()
    {
        return $this->_filesTree;
    }

    /**
     * Return theme file content from file
     * @return string
     * @throws Exception
     */
    public function fetchFileContent()
    {
        $fileName = $this->getFile();

        if (!$fileName) {
            return false;
        }

        $themeModel = $this->getThemeModel();

        $themeFilePath = $this->getPathToFile();
        $themeRealFilePath = $themeModel->getThemePath() . '/' . $fileName;

        $filePath = false;

        /** Custom Theme: Try to find file (1) in theme path */
        if ($themeModel::THEME_TYPE === 1 &&
            !file_exists($filePath = $themeFilePath)
        ) {
            $filePath = false;
        }

        /** Default Theme: Try to find file (1) in custom folder, (2) in real theme folder */
        if (
            $themeModel::THEME_TYPE === 0 &&
            !file_exists($filePath = $themeFilePath) &&
            !file_exists($filePath = $themeRealFilePath)
        ) {
            $filePath = false;
        }

        $fileContent = $filePath ? file_get_contents($filePath, false) : '';

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

        ConsoleHelper::execGenerateAssets();

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_THEMES_THEME_FILE_UPDATED, $this->_theme_model->id,  $this->_theme_model->name);

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


