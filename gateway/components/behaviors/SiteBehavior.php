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
 * Class SiteBehavior
 * @package gateway\components\behaviors
 */
class SiteBehavior extends Behavior {

    /**
     * @var Sites
     */
    protected $_gateway;

    /**
     * @var DefaultThemes
     */
    protected $_theme;

    /**
     * @var EditThemeForm
     */
    protected $_themeForm;

    /**
     * @var array
     */
    protected $_files;

    /**
     * @return EditThemeForm
     */
    protected function _getThemeForm()
    {
        if (null === $this->_themeForm) {
            $this->_themeForm = EditThemeForm::make($this->_getGatewayModel()->theme_name, null);
        }

        return $this->_themeForm;
    }

    /**
     * @return DefaultThemes
     */
    public function getThemeModel()
    {
        if (null === $this->_theme) {
            $form = $this->_getThemeForm();
            $this->_theme = $form ? $this->_getThemeForm()->getThemeModel() : null;
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
     * @return array
     */
    public function getThemeFiles()
    {
        if (null !== $this->_files) {
            return $this->_files;
        }

        $defaultTheme = $this->getThemeModel();

        if (!$defaultTheme) {
            return $this->_files = [];
        }

        $this->_files = ThemesFiles::find()
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

        return $this->_files;
    }
}