<?php
namespace sommerce\controllers;

use common\models\store\Blocks;
use common\models\stores\Stores;
use sommerce\helpers\BlockHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends CustomController
{
    /**
     * Error action
     * @return string
     */
    public function actionError()
    {
        $this->view->title = Yii::t('app', '404.title');

        return $this->renderPartialCustom('404.twig');
    }

    /**
     * Frozen action
     * @return string
     */
    public function actionFrozen()
    {
        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        if (!$store->isInactive()) {
            return $this->redirect('/');
        }

        return $this->renderPartial('frozen');
    }

    /**
     * Displays index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        $this->pageTitle = $store->seo_title;

        $blocks = [];
        foreach (Blocks::find()->all() as $block) {
            if ($store->isEnableBlock($block->code)) {
                $blocks[$block->code] = $block->getContent(BlockHelper::getDefaultBlock($block->code));
            }
        }

        return $this->render('index.twig', [
            'block' => $blocks
        ]);
    }

    /**
     * Displays checkout page.
     *
     * @return string
     */
    public function actionCheckout()
    {
        return $this->renderPartial('checkout');
    }
}
