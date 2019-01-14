<?php

namespace sommerce\modules\admin\controllers;

use common\models\stores\StoreAdminAuth;
use Yii;
use sommerce\controllers\CommonController;
use yii\web\User;

/**
 * Class AdminController
 * @package sommerce\modules\admin\controllers
 */
class AdminController extends CommonController
{
    /** @inheritdoc */
    public function beforeAction($action)
    {
        /** @var User $user */
        $user = Yii::$app->user;



        // Frozen/terminated store routine
        if ($this->store->isInactive() && !$user->isGuest) {

            /** @var StoreAdminAuth $identity */
            $identity = $user->getIdentity();

            if (!$identity->isSuperAdmin()) {
                $user->logout();
            }
        }

        return parent::beforeAction($action);
    }
}
