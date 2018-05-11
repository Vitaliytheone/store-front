<?php

namespace common\helpers;

use common\models\common\ProjectInterface;
use common\models\panels\Project;
use common\models\stores\Stores;

/**
 * Class ProjectHelper
 * @package common\helpers
 */
class ProjectHelper
{
    /**
     * Return Store or Panel by project type & project id
     * @param $type integer
     * @param $pid integer
     * @return null|Stores|Project
     */
    public static function getProjectByType($type, $pid)
    {
        switch ($type) {
            case ProjectInterface::PROJECT_TYPE_PANEL :
                $project = Project::findOne($pid);
                break;

            case ProjectInterface::PROJECT_TYPE_STORE :
                $project = Stores::findOne($pid);
                break;

            default :
                $project = null;
        }

        return $project;
    }

}