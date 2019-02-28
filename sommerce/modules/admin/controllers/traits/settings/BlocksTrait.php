<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use common\components\ActiveForm;
use common\models\sommerce\ActivityLog;
use common\models\sommerces\Stores;
use sommerce\controllers\CommonController;
use sommerce\helpers\BlockHelper;
use sommerce\modules\admin\models\forms\BlockUploadForm;
use sommerce\modules\admin\models\forms\UpdateBlocksForm;
use sommerce\modules\admin\models\search\LinksSearch;
use Yii;
use sommerce\modules\admin\models\search\BlocksSearch;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class BlocksTrait
 * @property CommonController $this
 * @package sommerce\modules\admin\controllers
 */
trait BlocksTrait {

    /**
     * Blocks page
     * @return string
     */
    public function actionBlocks()
    {
        $this->view->title = Yii::t('admin', "settings.blocks_page_title");

        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        $search = new BlocksSearch();
        $search->setStore($store);

        $this->addModule('adminBlocks');

        return $this->render('blocks', [
            'blocks' => $search->getBlocks()
        ]);
    }

    /**
     * Initialize Blocks ReactJs app
     * @param $code
     * @return mixed
     */
    public function actionEditBlock($code)
    {
        $this->view->title = Yii::t('admin', "settings.edit_block_page_title", [
            'block' => null
        ]);

        $this->layout = '@admin/views/layouts/react_app';

        return $this->render('edit_blocks_app', [
            'code' => $code,
        ]);
    }

    /**
     * Return product/page links for blocks AJAX action
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionGetBlockUrls()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Ajax request expected!');
        }
        $search = new LinksSearch();
        $search->setStore($this->store);

        return $search->searchLinks4Blocks();
    }

    /**
     * Return all blocks AJAX action
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionGetBlocks()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Ajax request expected!');
        }

        return BlockHelper::getBlocksContent();
    }

    /**
     * Update all blocks AJAX POST action
     * @return bool
     * @throws BadRequestHttpException
     * @throws \Throwable
     */
    public function actionUpdateBlocks()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$request->isAjax || !$request->isPost) {
            throw new BadRequestHttpException('Ajax request expected!');
        }

        /** @var \common\models\sommerces\StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        $form = new UpdateBlocksForm();
        $form->setUser($identity);
        $form->setBlocks($request->post());

        if (!$form->save()) {
            throw new BadRequestHttpException('Something was wrong! Try again later...');
        }

        return true;
    }

    /**
     * Upload block image
     * @param $code
     * @return array
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionBlockUpload($code)
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$request->isAjax || !$request->isPost) {
            throw new BadRequestHttpException('Ajax POST request expected!');
        }

        $block = BlockHelper::getBlock($code);

        if (!$block) {
            throw new NotFoundHttpException();
        }

        $model = new BlockUploadForm();
        $model->setBlock($block);

        if (!$model->save()) {
            throw new BadRequestHttpException(ActiveForm::firstError($model));
        }

        return ['url' => $model->link];
    }

    /**
     * Enable block
     * @param $code
     * @return array
     */
    public function actionEnableBlock($code)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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

        $this->_logToggleBlockStatus($code);

        return [
            'status' => 'success'
        ];
    }

    /**
     * Disable block
     * @param $code
     * @return array
     */
    public function actionDisableBlock($code)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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

        $this->_logToggleBlockStatus($code);

        return [
            'status' => 'success'
        ];
    }

    /**
     * Logging change block active status event
     * @param $code
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    private function _logToggleBlockStatus($code)
    {
        $block = BlockHelper::getBlock($code, true);

        if (!$block) {
            throw new NotFoundHttpException();
        }

        /** @var \common\models\sommerces\StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_BLOCKS_BLOCK_ACTIVE_STATUS_CHANGED, $block->id, $block->code);
    }
}