<?php
namespace superadmin\models\forms;

use common\models\sommerces\ExpiredLog;
use common\models\sommerces\Stores;
use Yii;
use common\models\sommerces\Project;
use yii\base\Model;

/**
 * Class EditStoreExpiryForm
 * @package superadmin\models\forms
 */
class EditStoreExpiryForm extends Model {

    public $expired;

    /**
     * @var Stores
     */
    private $_store;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['expired'], 'required'],
            [['expired'], 'date', 'format' => 'php:Y-m-d H:i:s']
        ];
    }

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Save expied
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $lastExpired = $this->_store->expired;
        $this->_store->expired = strtotime($this->expired) - Yii::$app->params['time'];

        if (!$this->_store->save(false)) {
            $this->addError('expired', Yii::t('app/superadmin', 'stores.modal.error_edit_expiry'));
            return false;
        }

        $expiredLogModel = new ExpiredLog();
        $expiredLogModel->attributes = [
            'pid' => $this->_store->id,
            'expired_last' => (int)$lastExpired,
            'expired' => $this->_store->expired,
            'created_at' => time(),
            'type' => ExpiredLog::TYPE_CHANGE_STORE_EXPIRY
        ];
        $expiredLogModel->save(false);

        return true;
    }


}