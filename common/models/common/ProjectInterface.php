<?php
namespace common\models\common;

interface ProjectInterface
{
    const PROJECT_TYPE_STORE = 1;
    const PROJECT_TYPE_PANEL = 2;

    /**
     * Return project type
     * @return integer
     */
    public static function getProjectType();

    /**
     * Return main domain of project (Project, Store...)
     * @return string|null
     */
    public function getBaseDomain();
}