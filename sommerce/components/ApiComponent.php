<?php

namespace sommerce\components;

use common\models\stores\StoreAdminAuth;
use common\models\stores\Stores;
use yii\base\Component;
use Yii;

class ApiComponent extends Component
{
    /**
     * Set user identity for react api by reactApiKey
     * @return bool
     */
    public function setIdentityForApi()
    {
        $user = Yii::$app->user;
        $store = $this->getStoreInstance();

        if (!$store->isInactive() && $user->isGuest && YII_ENV == 'dev') {
            $post = Yii::$app->getRequest()->post();
            $get = Yii::$app->getRequest()->get();

            if (
                (isset($post['key']) && $post['key'] === Yii::$app->params['reactApiKey']) ||
                (isset($get['key']) && $get['key'] === Yii::$app->params['reactApiKey'])
            ) {
                $userId = isset($post['user_id']) ? $post['user_id'] : (isset($get['user_id']) ? $get['user_id'] : null);
                if ($userId) {
                    $admin = StoreAdminAuth::findOne(['id' => $userId]);
                } else {
                    $admin = StoreAdminAuth::findOne(['status' => StoreAdminAuth::STATUS_ACTIVE]);
                }
                $user->setIdentity($admin);
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get store instance
     * @return mixed
     */
    private function getStoreInstance()
    {
        $store = Yii::$app->store->getInstance();

        if (!$store || !($store instanceof Stores)) {
            exit;
        }

        $store->checkExpired();
        return $store;
    }
}