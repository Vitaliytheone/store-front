<?php
namespace admin\models\forms;

use common\components\traits\UnixTimeFormatTrait;
use admin\models\search\ThemesSearch;
use common\models\gateway\ThemesFiles;
use common\models\gateways\DefaultThemes;
use common\models\gateways\Sites;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\User;
use yii\web\NotFoundHttpException;
use common\models\common\ThemesInterface;

/**
 * Class EditThemeForm
 * @package admin\models\forms
 */
class EditThemeForm extends Model
{
    use UnixTimeFormatTrait;

    public const FOLDER_LAYOUTS = 'Layouts';
    public const FOLDER_TEMPLATES = 'Templates';
    public const FOLDER_CSS = 'CSS';
    public const FOLDER_JS = 'JS';

    /**
     * Theme allowed folders/files structure
     * @var array
     */
    private $_filesTree = [
        self::FOLDER_LAYOUTS => [
            'layout.twig',
        ],
        self::FOLDER_TEMPLATES => [
            'index.twig',
            'page.twig',
            '404.twig',
        ],
        self::FOLDER_CSS => [
            'styles.css',
        ],
        self::FOLDER_JS => [
            'main.js',
        ],
    ];

    /** @var  string */
    public $file_content;

    /** @var ThemesInterface | DefaultThemes*/
    private $_theme_model;

    /** @var string Relative to theme folder filepath */
    private $_file;

    /** @var string Path to edited file */
    private $_path_to_file;

    /** @var  User */
    protected $_user;

    /** @var Sites */
    protected $_gateway;

    /**
     * @var array
     */
    protected $_themes;

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

        return $model;
    }

    /**
     * Return Theme model
     * @return DefaultThemes
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

        $themeFiles = [];//ThemesFilesHelper::dirTree($themePath, $themePath, '/^.*\.(css|js|twig|json)$/i');
        $customFiles = $this->getThemes();

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

        foreach ($this->_filesTree as $key => &$folder) {
            sort($folder);
        }

        foreach (ArrayHelper::getColumn($customFiles, 'name') as $file) {
            if (in_array($file, $this->_filesTree[static::FOLDER_LAYOUTS]) || in_array($file, $this->_filesTree[static::FOLDER_TEMPLATES])) {
                continue;
            }
            $this->_filesTree[static::FOLDER_TEMPLATES][] = $file;
        }

        // Populate files by files data
        foreach ($this->_filesTree as &$folder) {
            array_walk($folder, function(&$file) use ($themeFiles, $themeModel, $customFiles) {

                $modifiedAt = ArrayHelper::getValue($themeFiles, [$file, 'modified_at']);
                $isModified = false;

                if (!empty($customFiles[$file])) {
                    $isModified = true;
                    $modifiedAt = $customFiles[$file]['modified_at'];
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
        $customFiles = $this->getThemes();

        if (!empty($customFiles[$fileName])) {
            $fileContent = $customFiles[$fileName]['content'];
        } else {
            $themeFilePath = $this->getPathToFile();
            $themeRealFilePath = $themeModel->getThemePath() . '/' . $fileName;

            if (!file_exists($filePath = $themeFilePath) &&
                !file_exists($filePath = $themeRealFilePath)
            ) {
                $filePath = false;
            }

            $fileContent = $filePath ? file_get_contents($filePath, false) : '';
        }

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

        $themeModel = $this->getThemeModel();

        $attributes = [
            'theme_id' => $themeModel->id,
            'name' => $this->_file,
        ];

        if (!($model = ThemesFiles::findOne($attributes))) {
            $model = new ThemesFiles($attributes);
        }

        $model->content = $this->file_content;

        if (!$model->save()) {
            return false;
        }
        $model->refresh();

        return static::formatDate($model->updated_at, 'php:Y-m-d');
    }

    /**
     * Return is default theme file changed and it can be reset
     * @return bool
     */
    public function isResetAble()
    {
        $customThemes = $this->getThemes();

        return !empty($this->_file) && !empty($customThemes[$this->_file]);
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

    /**
     * @return array
     */
    public function getThemes()
    {
        if (null !== $this->_themes) {
            return $this->_themes;
        }

        $this->_themes = ThemesFiles::find()
            ->select([
                'name' => 'name',
                'content' => 'content',
                'modified_at' => 'updated_at',
            ])
            ->andWhere([
                'theme_id' => $this->getThemeModel()->id
            ])
            ->asArray()
            ->indexBy('name')
            ->all();

        return $this->_themes;
    }
}


