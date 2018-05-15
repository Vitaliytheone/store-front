<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\components\ActiveForm;
use common\models\store\ActivityLog;
use common\models\store\Blocks;
use common\models\stores\StoreAdminAuth;
use common\models\stores\Stores;
use sommerce\assets\BlocksAppAsset;
use sommerce\helpers\BlockHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\BlockUploadForm;
use sommerce\modules\admin\models\forms\EditBlockForm;
use sommerce\modules\admin\models\search\LinksSearch;
use Yii;
use sommerce\modules\admin\models\search\BlocksSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class BlocksTrait
 * @property Controller $this
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
            'block' => ''
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
            throw new BadRequestHttpException();
        }

        return (new LinksSearch())->searchLinks4Blocks();
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
            throw new BadRequestHttpException();
        }

        return BlockHelper::getDefaultBlocks();
    }

    /**
     * Update all blocks AJAX POST action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionUpdateBlocks()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        return true;
    }

    /**
     * Upload block image AJAX POST action
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionUploadBlockImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        return ['url' => 'http://static.euronews.com/articles/stories/03/02/58/22/1000x563_story-353f6fe8-c164-5129-b92c-2dc8a7f188d1_343372.jpg'];
    }


    /**
     * // TODO:: OLD EDIT! Delete it after finish!
     * Edit block
     * @param $code
     * @return array
     */
    public function __actionEditBlock($code)
    {
        $block = $this->_findBlock($code, true);

        $this->view->title = Yii::t('admin', "settings.edit_block_page_title", [
            'block' => $code
        ]);

        $model = new EditBlockForm();
        $model->setBlock($block);
        $model->setUser(Yii::$app->user);

        if (Yii::$app->request->isPost) {
            $model->content = Yii::$app->request->post('content');
            $save = $model->save();

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if (!$save) {
                    return [
                        'status' => 'error',
                        'error' => ActiveForm::firstError($model)
                    ];
                }

                return [
                    'status' => 'success'
                ];
            }

            if ($save) {
                return $this->refresh();
            }
        }

        $this->layout = 'block';

        $this->addModule('adminEditBlock', [
            'code' => $code,
            'saveUrl' => Url::toRoute(['settings/edit-block', 'code' => $code]),
            'uploadUrl' => Url::toRoute(['settings/block-upload', 'code' => $code]),
            'block' => $block->getContent(BlockHelper::getDefaultBlock($code))
        ]);

        return $this->render('edit_block', [
            'code' => $code,
        ]);
    }

    /**
     * Edit block
     * @param $code
     * @return array
     */
    public function actionBlockUpload($code)
    {
        $block = $this->_findBlock($code);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new BlockUploadForm();
        $model->setBlock($block);

        if (Yii::$app->request->isPost) {
            $model->load([
                $model->formName() => Yii::$app->request->post()
            ]);
            $save = $model->save();

            if (!$save) {
                return [
                    'status' => 'error',
                    'error' => ActiveForm::firstError($model)
                ];
            }

            return [
                'status' => 'success',
                'link' => $model->link
            ];
        }

        return [
            'status' => 'error',
            'error' => ''
        ];
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
     * Find block by code or create new
     * @param string $code
     * @param bool $createOnEmpty
     * @return null|Blocks
     * @throws NotFoundHttpException
     */
    public function _findBlock($code, $createOnEmpty = false)
    {
        $block = null;
        if (!($block = Blocks::findOne([
            'code' => $code
        ]))) {

            if (!$createOnEmpty || !in_array($code, Blocks::getCodes())) {
                throw new NotFoundHttpException();
            }

            $block = new Blocks();
            $block->code = $code;
            $block->setContent(BlockHelper::getDefaultBlock($block->code));
            $block->save(false);
        }

        return $block;
    }

    /**
     * Logging change block active status event
     * @param $code
     */
    private function _logToggleBlockStatus($code)
    {
        $block = $this->_findBlock($code);

        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_BLOCKS_BLOCK_ACTIVE_STATUS_CHANGED, $block->id, $block->code);
    }
}