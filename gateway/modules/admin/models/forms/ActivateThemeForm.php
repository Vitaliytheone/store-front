<?php
namespace admin\models\forms;

use common\models\gateways\DefaultThemes;
use common\models\gateways\Sites;
use admin\models\search\ThemesSearch;
use Yii;
use yii\base\Exception;
use yii\web\User;

/**
 * Class ActivateThemeForm
 * @package admin\models\forms
 */
class ActivateThemeForm
{

    /**
     * @var User
     */
    protected $_user;

    /**
     * @var Sites
     */
    protected $_gateway;

    /**
     * Set current gateway
     * @param Sites $gateway
     */
    public function setGateway(Sites $gateway)
    {
        $this->_gateway = $gateway;
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
     * @return DefaultThemes|false
     * @throws Exception
     */
    public function activate($themeFolder)
    {
        $search = new ThemesSearch();
        $search->setGateway($this->_gateway);
        $themeModel = $search->searchByFolder($themeFolder);
        if (!$themeModel) {
            throw new Exception('Theme does not exist in DB!');
        }

        $themePath = $themeModel->getThemePath();

        if (!file_exists($themePath)) {
            throw new Exception('Theme folder does not exist in filesystem!');
        }

        $this->_gateway->setAttributes([
            'theme_name' => $themeModel->name,
            'theme_folder' => $themeModel->folder,
        ]);

        if (!$this->_gateway->save(true, ['theme_name', 'theme_folder'])) {
            throw new Exception('Could not update active theme settings!');
        }

        return $themeModel;
    }
}
