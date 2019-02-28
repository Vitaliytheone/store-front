<?php

namespace sommerce\modules\admin\models\forms;

use common\models\sommerce\ActivityLog;
use common\models\sommerces\StoreAdminAuth;
use console\helpers\ConsoleHelper;
use common\models\sommerce\CustomThemes;
use common\models\sommerces\DefaultThemes;
use common\models\sommerces\Stores;
use sommerce\modules\admin\models\search\ThemesSearch;
use Yii;
use yii\base\Exception;
use yii\web\User;

/**
 * Class ActivateThemeForm
 * @package sommerce\modules\admin\models\forms
 */
class ActivateThemeForm
{

    /**
     * @var User
     */
    protected $_user;

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * Set current store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
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
     * Activate theme by theme folder
     * @param $themeFolder
     * @return CustomThemes|DefaultThemes|false
     * @throws Exception
     */
    public function activate($themeFolder)
    {
        $search = new ThemesSearch();
        $search->setStore($this->_store);
        $themeModel = $search->searchByFolder($themeFolder);
        if (!$themeModel) {
            throw new Exception('Theme does not exist in DB!');
        }

        $themePath = $themeModel->getThemePath();

        if (!file_exists($themePath)) {
            throw new Exception('Theme folder does not exist in filesystem!');
        }

        $this->_store->setAttributes([
            'theme_name' => $themeModel->name,
            'theme_folder' => $themeModel->folder,
        ]);

        if (!$this->_store->save()) {
            throw new Exception('Could not update active theme settings!');
        }

        ConsoleHelper::execConsoleCommand('system-sommerce/generate-assets');

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity();

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_THEMES_THEME_ACTIVATED, $themeModel->id, $themeModel->name);

        return $themeModel;
    }
}
