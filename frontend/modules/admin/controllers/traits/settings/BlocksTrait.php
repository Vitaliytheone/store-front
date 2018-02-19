<?php
namespace frontend\modules\admin\controllers\traits\settings;

use common\components\ActiveForm;
use common\models\store\Blocks;
use common\models\stores\Stores;
use frontend\modules\admin\components\Url;
use frontend\modules\admin\models\forms\BlockUploadForm;
use frontend\modules\admin\models\forms\EditBlockForm;
use Yii;
use frontend\modules\admin\models\search\BlocksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
     * Edit block
     * @param $code
     */
    public function actionEditBlock($code)
    {
        $block = $this->_findBlock($code);

        $this->view->title = Yii::t('admin', "settings.edit_block_page_title", [
            'block' => $code
        ]);

        $model = new EditBlockForm();
        $model->setBlock($block);

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
            'block' => $block->getContent()
        ]);

        return $this->render('edit_block', [
            'code' => $code,
        ]);
    }

    /**
     * Edit block
     * @param $code
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

        return [
            'status' => 'success'
        ];
    }

    /**
     * Disable block
     * @param $code
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

        return [
            'status' => 'success'
        ];
    }

    /**
     * Find block by code
     * @param $code
     * @return null|Blocks
     * @throws NotFoundHttpException
     */
    public function _findBlock($code)
    {
        $block = null;
        if (!($block = Blocks::findOne([
            'code' => $code
        ]))) {
            throw new NotFoundHttpException();
        }

        return $block;
    }
}