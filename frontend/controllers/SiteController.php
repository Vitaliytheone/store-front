<?php
namespace frontend\controllers;

use common\models\store\Blocks;
use common\models\stores\Stores;
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

        return $this->renderPartial('404');
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
                $blocks[$block->code] = $block->getContent();
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
