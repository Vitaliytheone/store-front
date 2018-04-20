<?php
namespace common\models\common;

interface ProjectInterface
{
    /**
     * Return main domain of project (Project, Store...)
     * @return string|null
     */
    public function getBaseDomain();
}