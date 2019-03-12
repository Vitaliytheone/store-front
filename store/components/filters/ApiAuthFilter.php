<?php
namespace store\components\filters;

use common\models\stores\StoreAdminAuth;
use yii\base\ActionFilter;
use Yii;

/**
 * ApiAuthFilter
 *
 * Example:
 *
 * public function behaviors()
 * {
 *     return => [
 *         [
 *             'class' => ApiAuthFilter::class,
 *             'only' => ['view', 'index'],
 *         ],
 *     ];
 * }
 *
 * Class ApiAuthFilter
 * @package store\components\filters
 */
class ApiAuthFilter extends ActionFilter
{

    /**
     * {@inheritdoc}
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $user = Yii::$app->user;

        if ($user->isGuest && YII_ENV == 'dev') {
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
                Yii::$app->controller->enableCsrfValidation = false;
                Yii::$app->controller->enableDomainValidation = false;
            }
        }

        return parent::beforeAction($action);
    }
}