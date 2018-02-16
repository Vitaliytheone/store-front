<?php
namespace frontend\modules\admin\controllers\traits\settings;

use common\models\stores\Stores;
use Yii;
use frontend\modules\admin\models\search\BlocksSearch;
use yii\web\Controller;

/**
 * Class BlocksTrait
 * @property Controller $this
 * @package frontend\modules\admin\controllers
 */
trait BlocksTrait {

    /**
     * Blocks page
     * @return string
     */
    public function actionBlocks()
    {
        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        $search = new BlocksSearch();
        $search->setStore($store);

        return $this->render('blocks', [
            'blocks' => $search->getBlocks()
        ]);
    }

    /**
     * Edit block
     * @param $code
     */
    public function actionEditBlock($code)
    {

    }

    /**
     * Enable block
     * @param $code
     */
    public function actionEnable($code)
    {
        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        $attributeName = 'block_' . $code;

        if (!$store->hasAttribute($attributeName)) {
            return [
                'status' => 'error'
            ];
        }

        $store->setAttribute($attributeName, 1);
        $store->save(false);

        return [
            'status' => 'success'
        ];
    }

    /**
     * Disable block
     * @param $code
     */
    public function actionDisable($code)
    {
        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        $attributeName = 'block_' . $code;

        if (!$store->hasAttribute($attributeName)) {
            return [
                'status' => 'error'
            ];
        }

        $store->setAttribute($attributeName, 0);
        $store->save(false);

        return [
            'status' => 'success'
        ];
    }
}