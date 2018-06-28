<?php

namespace my\modules\superadmin\models\search\dashboard;

use Exception;
use my\modules\superadmin\components\services\BaseService;

class DashboardService
{
    private $source;
    private $name;
    
    /**
     * DashboardService constructor.
     * @param BaseService $source
     * @param $name
     */
    public function __construct(BaseService $source, $name)
    {
        $this->source = $source;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->source->getBalance();
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->source->hasError();
    }

    /**
     * @return Exception
     */
    public function getError()
    {
        return $this->source->getError();
    }
}