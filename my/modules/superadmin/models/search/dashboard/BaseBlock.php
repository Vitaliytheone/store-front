<?php

namespace my\modules\superadmin\models\search\dashboard;

use Yii;

/*
 * Abstract source class for dashboard services
 */
abstract class BaseBlock
{
    protected  $name;
    protected static $instance;
    
    /**
     * @return string
     */
    public  function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /*
     * Get instance
     * @return BasePanel
     */
    public static function getInstance()
    {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    abstract protected static function getQuery();

    /**
     * @return int
     * get midnight time
     */
    protected static function _getFilterTime()
    {
        return strtotime('today midnight') - (int) Yii::$app->params['time'];
    }

    /**
     * @param $time
     * @return string
     */
    protected static function _formatDate($time)
    {
        return gmdate("Y-m-d h:m:s", (int) ((int)$time + Yii::$app->params['time']));
    }

    /**
     * Get entities data
     * @return array
     */
    abstract public static function getEntities();

    /**
     * Get count of entities
     * @return int
     */
    abstract public static function getCount();

}