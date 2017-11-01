<?php
namespace common\components\stores;

use Yii;
use common\models\stores\Stores;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class StoreComponent
 * @package app\components\stores
 */
class StoreComponent extends Component
{
    private static $_instance;

    /**
     * @var string - current store domain
     */
    public $domain;

    /**
     * Get current store model
     * @return Stores
     */
    public function getInstance()
    {
        if (null == static::$_instance) {
            if (Yii::$app->params['storeId']) {
                $attributes = Yii::$app->params['storeId'];
            } elseif ($this->domain) {
                $domain = $this->domain;

                $attributes = [
                    'domain' => $domain
                ];
            } else {
                $domain = Yii::$app->request->hostName;
                $domain = preg_replace('/^www\./i', '', $domain);

                $attributes = [
                    'domain' => $domain
                ];
            }

            $store = Stores::findOne($attributes);

            if ($store) {
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
        Yii::$app->storeDb->dsn = 'mysql:host=localhost;dbname=' . ArrayHelper::getValue($this->getInstance(), 'db_name');
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