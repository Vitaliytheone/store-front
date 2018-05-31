<?php

namespace common\helpers;

use common\models\common\ProjectInterface;
use common\models\panels\Project;
use common\models\stores\Stores;
use yii\helpers\ArrayHelper;
use Yii;

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

    /**
     * Return project types
     * @return array
     */
    public static function getProjectTypes()
    {
        return [
            ProjectInterface::PROJECT_TYPE_PANEL => Yii::t('app', 'project.type.panel'),
            ProjectInterface::PROJECT_TYPE_STORE => Yii::t('app', 'project.type.store'),
        ];
    }

    /**
     * Return project type name by project_type
     * @param $type
     * @return mixed
     */
    public static function getProjectTypeName($type)
    {
        return ArrayHelper::getValue(static::getProjectTypes(), $type, null);
    }
}