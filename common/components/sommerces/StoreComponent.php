<?php
namespace common\components\sommerces;

use common\models\sommerces\StoreDomains;
use Yii;
use common\models\sommerces\Stores;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class StoreComponent
 * @package common\components\sommerces
 */
class StoreComponent extends Component
{
    private static $_instance;

    /**
     * @var string - current store domain
     */
    public $domain;

    public function init()
    {
        $this->getInstance();
        return parent::init();
    }

    /**
     * Get current store model
     * @return Stores
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

            $domainModel = $domain ? StoreDomains::findOne(['domain' => $domain]) : null;

            if ($domainModel && $domainModel instanceof StoreDomains) {
                $store = $domainModel->store;
            }

            if ($store instanceof Stores) {
                $this->setInstance($store);
            }
        }

        return static::$_instance;
    }

    /**
     * Set instance
     * @param Stores $store
     */
    public function setInstance($store)
    {
        static::$_instance = $store;
        $this->initDb();
    }

    /**
     * Init db
     */
    public function initDb()
    {
        Yii::$app->storeDb->close();
        Yii::$app->storeDb->dsn .= ';dbname=' . ArrayHelper::getValue($this->getInstance(), 'db_name');
    }

    /**
     * Get panel id
     * @return mixed
     */
    public function getId()
    {
        return ArrayHelper::getValue($this->getInstance(), 'id');
    }
}