<?php
namespace common\components\gateways;

use common\models\gateways\Sites;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class GatewayComponent
 * @package common\components\gateways
 */
class GatewayComponent extends Component
{
    private static $_instance;

    /**
     * @var string - current gateway domain
     */
    public $domain;

    public function init()
    {
        $this->getInstance();
        return parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * Get current store model
     * @return Sites
     */
    public function getInstance()
    {
        if (null === static::$_instance) {

            $store = $domain = null;

            if ($this->domain) {
                $domain = $this->domain;
            } else if (!empty(Yii::$app->request->hostName)) {
                $domain = Yii::$app->request->hostName;
                $domain = preg_replace('/^www\./i', '', $domain);
            }

            $site = $domain ? Sites::findOne(['domain' => $domain]) : null;

            if ($site instanceof Sites) {
                $this->setInstance($site);
            }
        }

        return static::$_instance;
    }

    /**
     * Set instance
     * @param Sites $site
     */
    public function setInstance($site)
    {
        static::$_instance = $site;
        $this->initDb();
    }

    /**
     * Init db
     */
    public function initDb()
    {
        Yii::$app->gatewayDb->close();
        Yii::$app->gatewayDb->dsn .= ';dbname=' . ArrayHelper::getValue($this->getInstance(), 'db_name');
    }

    /**
     * Get site id
     * @return mixed
     */
    public function getId()
    {
        return ArrayHelper::getValue($this->getInstance(), 'id');
    }
}