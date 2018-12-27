<?php

namespace gateway\components\behaviors;

use admin\models\forms\EditThemeForm;
use common\models\gateway\Pages;
use common\models\gateway\ThemesFiles;
use common\models\gateways\DefaultThemes;
use common\models\gateways\Sites;
use yii\helpers\ArrayHelper;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Class PageBehavior
 * @package gateway\components\behaviors
 */
class PageBehavior extends Behavior {

    public const DEFAULT_PAGE_TEMPLATE_FILE = 'page.twig';

    /**
     * @var Sites
     */
    protected $_gateway;

    /**
     * @var EditThemeForm
     */
    protected $_themeForm;

    /**
     * @var DefaultThemes
     */
    protected $_theme;

    /**
     * @var ThemesFiles
     */
    protected $_themeFile;

    /**
     * @var Pages
     */
    protected $_page;

    public function events()
    {
        return ArrayHelper::merge(parent::events(), [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterAddTwig',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdateTwig',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdateTwig',
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'getPageModel',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDeleteTwig',
        ]);
    }

    /**
     * Add twig
     */
    public function afterAddTwig()
    {
        $this->_themeFile = new ThemesFiles([
            'theme_id' => $this->_getThemeModel()->id,
            'content' => $this->getTwigContent(),
            'name' => $this->getTwigName(),
        ]);
        return $this->_themeFile->save();
    }

    /**
     * Find existed twig
     */
    public function beforeUpdateTwig()
    {
        $oldPage = Pages::findOne($this->getPageModel()->id);

        $this->_themeFile = ThemesFiles::findOne([
            'theme_id' => $this->_getThemeModel()->id,
            'name' => $oldPage->getTwigName(),
        ]);
    }

    public function afterUpdateTwig()
    {
        if (empty($this->_themeFile)) {
            return $this->afterAddTwig();
        }

        $this->_themeFile->content = $this->getTwigContent();
        $this->_themeFile->name = $this->getTwigName();
        return $this->_themeFile->save();
    }

    /**
     * @return int
     */
    public function afterDeleteTwig()
    {
        return ThemesFiles::deleteAll([
            'theme_id' => $this->_getThemeModel()->id,
            'name' => $this->getPageModel()->getTwigName(),
        ]);
    }

    /**
     * @return string
     */
    public function getDefaultContent()
    {
        return $this->_getThemeForm()->fetchFileContent();
    }

    /**
     * @return EditThemeForm
     */
    protected function _getThemeForm()
    {
        if (null === $this->_themeForm) {
            $this->_themeForm = EditThemeForm::make($this->_getGatewayModel()->theme_name, static::DEFAULT_PAGE_TEMPLATE_FILE);
        }

        return $this->_themeForm;
    }

    /**
     * @return DefaultThemes
     */
    protected function _getThemeModel()
    {
        if (null === $this->_theme) {
            $this->_theme = $this->_getThemeForm()->getThemeModel();
        }

        return $this->_theme;
    }

    /**
     * @return Sites
     */
    protected function _getGatewayModel()
    {
        if (null === $this->_gateway) {
            $this->_gateway = Yii::$app->gateway->getInstance();
        }

        return $this->_gateway;
    }

    /**
     * @return string
     */
    public function getTwigName()
    {
        return $this->getPageModel()->url . '.twig';
    }

    /**
     * @return string
     */
    public function getTwigContent()
    {
        return $this->getPageModel()->content;
    }

    /**
     * @return Pages|null
     */
    public function getPageModel()
    {
        if (null === $this->_page) {
            $this->_page = $this->owner;
        }

        return $this->_page;
    }
}