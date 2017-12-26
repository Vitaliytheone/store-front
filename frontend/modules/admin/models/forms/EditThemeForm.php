<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use common\models\stores\Stores;
use frontend\helpers\CustomFilesHelper;
use frontend\modules\admin\models\search\ThemesSearch;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class EditThemeForm
{
    private $_theme_model;
    private $_file;

    public function __construct(array $config)
    {
        if (isset($config['folder'])) {
            $this->_theme_model = (new ThemesSearch())->searchByFolder($config['folder']);

            if (!$this->_theme_model) {
                throw new Exception('Theme not found!');
            }
        }

        if (isset($config['file'])) {
            $this->_file = $config['file'];
        }
    }

    /**
     * Return Theme model
     * @return CustomThemes|DefaultThemes|false
     */
    public function getThemeModel()
    {
        return $this->_theme_model;
    }

    /**
     * Return theme files tree
     * @return array
     * @throws Exception
     */
    public function getFilesTree()
    {
        if (!$this->_theme_model) {
            throw new Exception('Theme not found!');
        }

        return CustomFilesHelper::dirTree($this->_theme_model->getThemePath(), '/^.*\.(twig|css|json|js)$/i');
    }

}
