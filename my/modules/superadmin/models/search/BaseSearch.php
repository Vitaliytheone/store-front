<?php

namespace superadmin\models\search;

use common\models\panels\Project;
use yii\base\Model;

/**
 * Class BaseSearch
 * @package superadmin\models\search
 */
class BaseSearch extends Model
{
    /**
     * @var Project
     */
    protected $_panel;

    /**
     * @return Project
     */
    public function getPanel()
    {
        return $this->_panel;
    }

    /**
     * @param Project $panel
     */
    public function setPanel(Project $panel)
    {
        $this->_panel = $panel;
        $this->attributes = $panel->attributes;
    }
}