<?php
namespace admin\models\forms;

use common\models\sommerces\StoreAdminAuth;
use common\models\sommerces\Stores;
use yii\base\Model;

/**
 * Class BaseForm
 * @package admin\models
 */
class BaseForm extends Model {

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * @var StoreAdminAuth
     */
    protected $_user;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Return store
     * @return Stores
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * @param StoreAdminAuth $user
     */
    public function setUser(StoreAdminAuth $user)
    {
        $this->_user = $user;
    }

    /**
     * @return StoreAdminAuth
     */
    public function getUser()
    {
        return $this->_user;
    }

}