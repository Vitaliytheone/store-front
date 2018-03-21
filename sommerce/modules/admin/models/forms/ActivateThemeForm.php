<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use console\helpers\ConsoleHelper;
use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use common\models\stores\Stores;
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
        $themeModel = (new ThemesSearch())->searchByFolder($themeFolder);
        if (!$themeModel) {
            throw new Exception('Theme does not exist in DB!');
        }

        $themePath = $themeModel->getThemePath();

        if (!file_exists($themePath)) {
            throw new Exception('Theme folder does not exist in filesystem!');
        }

        /** @var Stores $storeModel */
        $storeModel = Yii::$app->store->getInstance();

        $storeModel->setAttributes([
            'theme_name' => $themeModel->name,
            'theme_folder' => $themeModel->folder,
        ]);

        if (!$storeModel->save()) {
            throw new Exception('Could not update active theme settings!');
        }

        ConsoleHelper::execConsoleCommand('system-sommerce/generate-assets');

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity();

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_THEMES_THEME_ACTIVATED, $themeModel->id, $themeModel->name);

        return $themeModel;
    }
}
