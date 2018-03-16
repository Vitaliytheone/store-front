<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\DefaultThemes;
use common\models\stores\StoreAdminAuth;
use common\models\stores\Stores;
use Yii;
use yii\base\Exception;
use yii\behaviors\AttributeBehavior;
use common\models\store\CustomThemes;
use yii\web\User;

/**
 * Class CreateThemeForm
 * @package sommerce\modules\admin\models\forms
 */
class CreateThemeForm extends CustomThemes
{
    /** @var  Stores */
    private $_store;

    /**
     * @var User
     */
    protected $_user;

    public function init()
    {
        $this->_store = Yii::$app->store->getInstance();

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'folder' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => 'folder',
                ],
                'value' => function ($event) {
                    return $this->generateFolderName();
                },
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'ThemeForm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            ['name', 'trim'],
            ['folder', 'safe'],
            [['name', 'folder'], 'string', 'max' => 300],
            [['created_at', 'updated_at'], 'integer'],
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
     * Create new Theme
     * @param $postData
     * @return bool
     * @throws Exception
     */
    public function create($postData)
    {
        if (!$this->load($postData) || !$this->validate()) {
            return false;
        }

        // Create theme folder
        if (!mkdir($this->getThemePath(), 0755, true)) {
            throw new Exception('Could not create theme folder!');
        };

        // Copy template files
        $srcPath = static::getDefaultThemePath();
        $dstPath = $this->getThemePath();

        if (!file_exists($srcPath)) {
            throw new Exception('Template source path does not exist!');
        }

        $cmdCp = "cp -a $srcPath/. $dstPath/";
        exec($cmdCp, $copies, $cpCmdRes);
        if ($cpCmdRes != 0) {
            throw new Exception('Could not copy theme templates!');
        }

        // Check copied files
        $cmdDiff = "diff -a $srcPath $dstPath";
        exec($cmdDiff, $diffs, $difCmdRes);
        if ($difCmdRes != 0) {
            throw new Exception('Not all files have been copied!');
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity();

        if (!$this->save(false)) {
            return false;
        }

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_THEMES_THEME_ADDED, $this->id, $this->name);

        return true;
    }

    /**
     * Generate unique theme folder name from theme name
     * @return string
     */
    public function generateFolderName()
    {
        // Clean from non letter-numeric chars, replace spaces by _
        $folderName = strtolower(trim($this->name));
        $folderName = preg_replace("/[^ \w]+/", "", trim($folderName));
        $folderName = preg_replace('/\s+/', '_', $folderName);
        $folderName = self::THEME_PREFIX . $folderName;

        if (!static::existThemeFolder($folderName)) {
            return $folderName;
        }

        $themePostfix = 1;
        do {
            $newFolderName = $folderName . "-$themePostfix";
            $themePostfix ++;
        } while (static::existThemeFolder($newFolderName));

        return $newFolderName;
    }

    /**
     * Check if theme folder already exist
     * @param $folderName
     * @return bool
     */
    public static function existThemeFolder($folderName)
    {
        $customThemePath = static::getThemesPath() . '/' . $folderName;
        $defaultThemePath = DefaultThemes::getThemesPath() . '/' . $folderName;

        return file_exists($customThemePath) || file_exists($defaultThemePath);
    }

}
