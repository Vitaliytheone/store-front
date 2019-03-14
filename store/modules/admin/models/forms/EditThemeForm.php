<?php

namespace store\modules\admin\models\forms;

use common\components\traits\UnixTimeFormatTrait;
use common\models\store\ActivityLog;
use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use common\models\stores\StoreAdminAuth;
use common\models\stores\Stores;
use console\helpers\ConsoleHelper;
use store\helpers\CustomFilesHelper;
use store\modules\admin\models\search\ThemesSearch;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\User;
use yii\web\NotFoundHttpException;
use common\models\common\ThemesInterface;

/**
 * Class EditThemeForm
 * @package store\modules\admin\models\forms
 */
class EditThemeForm extends Model
{
    use UnixTimeFormatTrait;

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
            'vieworder.twig',
        ],
        'Snippets' => [
            'slider.twig',
            'features.twig',
            'reviews.twig',
            'process.twig',
        ],
        'JS' => [],
        'CSS' => [],
        'configs' => [],
    ];

    /** @var  string */
    public $file_content;

    /** @var ThemesInterface | CustomThemes | DefaultThemes*/
    private $_theme_model;

    /** @var string Relative to theme folder filepath */
    private $_file;

    /** @var string Path to edited file */
    private $_path_to_file;

    /** @var  User */
    protected $_user;

    /** @var  Stores */
    protected $_store;

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
     * @param $themeEditFileName
     * @return bool
     * @throws NotFoundHttpException
     */
    public function setFile($themeEditFileName)
    {
        if (!$this->_theme_model) {
            return false;
        }

        /** Check is filename is allowed */
        if (strpos(json_encode($this->getFilesTree()), $themeEditFileName) === false) {
            throw new NotFoundHttpException();
        }

        $this->_file = $themeEditFileName;

        /**
         * For Custom theme — save file to theme path
         * For Default theme — save file to custom themes path
         */
        if (($this->_theme_model)::getThemeType() === ($this->_theme_model)::THEME_TYPE_CUSTOM) {
            $this->_path_to_file = $this->_theme_model->getThemePath() . '/' . $this->_file;
        } else {
            $this->_path_to_file = $this->_theme_model->getSaveToPath() . '/' . $this->_file;
        }
        return true;
    }

    /**
     * Create Form model
     * @param $themeFolderName
     * @param $themeEditFileName
     * @param Stores $store
     * @return bool|EditThemeForm
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
        if ($themeModel::getThemeType() === $themeModel::THEME_TYPE_CUSTOM) {
            $model->_path_to_file = $themeModel->getThemePath() . '/' . $model->_file;
        } else {
            $model->_path_to_file = $themeModel->getSaveToPath() . '/' . $model->_file;
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
        $themeModel = $this->getThemeModel();
        $themePath = $themeModel->getThemePath();

        $themeCustomFiles = [];

        $themeFiles = CustomFilesHelper::dirTree($themePath, $themePath, '/^.*\.(css|js|twig|json)$/i');

        if ($themeModel::getThemeType() === $themeModel::THEME_TYPE_DEFAULT) {
            $customFilePath = $themeModel->getSaveToPath();

            if (file_exists($customFilePath)) {
                $themeCustomFiles = CustomFilesHelper::dirTree($customFilePath, $customFilePath, '/^.*\.(css|js|twig|json)$/i');
            }
        }

        $themeFiles = array_merge($themeFiles, $themeCustomFiles);

        foreach ($themeFiles as $fileName => $fileData) {
            $extension = ArrayHelper::getValue($fileData, 'extension');

            if ($extension === 'js') {
                $this->_filesTree['JS'][] = $fileName;
            }
            if ($extension === 'css') {
                $this->_filesTree['CSS'][] = $fileName;
            }

            if ($fileName === 'data.json' || $fileName === 'settings.json') {
                $this->_filesTree['configs'][] = $fileName;
            }
        }

        foreach ($this->_filesTree as $key=>&$folder)
        {
            sort($folder);
        }

        // Populate files by files data
        foreach ($this->_filesTree as &$folder) {
            array_walk($folder, function(&$file) use ($themeFiles, $themeModel, $themeCustomFiles) {

                $modifiedAt = ArrayHelper::getValue($themeFiles, [$file, 'modified_at']);

                if ($themeModel::getThemeType() === $themeModel::THEME_TYPE_DEFAULT) {
                    $isModified = in_array($file, array_keys($themeCustomFiles));
                } else {
                    $isModified = (int)$modifiedAt > (int)$themeModel->created_at;
                }

                $file = [
                    'name' => $file,
                    'modified_at' => $isModified ? static::formatDate($modifiedAt, 'php:Y-m-d') : null,
                    'is_modified' => $isModified,
                ];
            });
        }

        return $this->_filesTree;
    }

    /**
     * Return theme files tree
     * @return array
     */
    public function getFilesTree()
    {
        return $this->_filesTree;
    }

    /**
     * Return theme files array of folders names
     * @return array
     */
    public function getFolders()
    {
        return array_keys($this->_filesTree);
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
        if ($themeModel::getThemeType() === $themeModel::THEME_TYPE_CUSTOM &&
            !file_exists($filePath = $themeFilePath)
        ) {
            $filePath = false;
        }

        /** Default Theme: Try to find file (1) in custom folder, (2) in real theme folder */
        if (
            $themeModel::getThemeType() === $themeModel::THEME_TYPE_DEFAULT &&
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
     * @param array|string $data
     * @return bool|string
     */
    public function updateThemeFile($data)
    {
        if (is_string($data)) {
            $this->file_content = $data;
        } else {
            if (!$this->load($data)) {
                return false;
            }
        }

        $pathToFile = $this->getPathToFile();
        $path = dirname($pathToFile);

        if (!file_exists($path)) {
            mkdir($path, 0766, true);
        }


        if (!file_put_contents($pathToFile, $this->file_content)) {
            return false;
        }

        ConsoleHelper::execConsoleCommand('system-store/generate-assets');

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_THEMES_THEME_FILE_UPDATED, $this->_theme_model->id,  $this->_theme_model->name);

        $modifiedAt = @filemtime($pathToFile);

        if (!$modifiedAt) {
            return false;
        }

        return static::formatDate($modifiedAt, 'php:Y-m-d');
    }

    /**
     * Return is default theme file changed and it can be reset
     * @return bool
     */
    public function isResetAble()
    {
        $themeModel = $this->getThemeModel();

        $file = $this->getPathToFile();

        return $themeModel::getThemeType() === $themeModel::THEME_TYPE_DEFAULT && $file && file_exists($file);
    }

    /**
     * Return file last edit date
     * @return bool|int
     */
    public function getModifiedDate()
    {
        $file = $this->getPathToFile();

        if (!file_exists($file) || !$editDate = @filemtime($file)) {
            return null;
        }

        return static::formatDate($editDate);
    }

}


