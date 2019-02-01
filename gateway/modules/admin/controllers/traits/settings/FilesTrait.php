<?php
namespace admin\controllers\traits\settings;

use admin\components\Url;
use admin\models\forms\CreateFileForm;
use admin\models\forms\EditFileForm;
use admin\models\forms\RenameFileForm;
use admin\models\forms\UploadFileForm;
use admin\models\search\FilesSearch;
use common\components\ActiveForm;
use common\models\gateway\Files;
use gateway\controllers\CommonController;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class FilesTrait
 * @property CommonController $this
 * @package admin\controllers
 */
trait FilesTrait {

    /**
     * Files listing and edit file
     * @param integer $id Relative file path to current Theme dir
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFiles($id = null)
    {
        $this->view->title = Yii::t('admin', 'settings.files_edit_title');
        $file = null;
        $gateway = Yii::$app->gateway->getInstance();

        $model = new EditFileForm();
        $model->setGateway($gateway);

        if ($id) {
            $file = $this->_findModel($id, Files::class);
            $model->setFile($file);
        }

        $this->addModule('adminFiles');

        return $this->render('edit_file', [
            'file' => $file,
            'model' => $model,
            'files' => (new FilesSearch())->setGateway($gateway)->search(),
        ]);
    }

    /**
     * Update file content
     * @param integer $id
     * @return array
     */
    public function actionUpdateFile($id)
    {
        $file = $this->_findModel($id, Files::class);

        if (!Files::can(Files::CAN_UPDATE, $file)) {
            return [
                'status' => 'error',
                'message' => Yii::t('admin', 'settings.files_error'),
            ];
        }

        $gateway = Yii::$app->gateway->getInstance();

        $model = new EditFileForm();
        $model->setGateway($gateway);
        $model->setFile($file);

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                return [
                    'status' => 'success',
                    'redirect' => Url::toRoute(['settings/files', 'id' => $file->id]),
                    'message' => Yii::t('admin', 'settings.files_message_updated'),
                ];
            }

            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model),
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('admin', 'settings.files_error'),
        ];
    }

    /**
     * Rename file
     * @param integer $id
     * @return array
     */
    public function actionRenameFile($id)
    {
        $file = $this->_findModel($id, Files::class);

        if (!Files::can(Files::CAN_RENAME, $file)) {
            return [
                'status' => 'error',
                'message' => Yii::t('admin', 'settings.files_error'),
            ];
        }

        $gateway = Yii::$app->gateway->getInstance();

        $model = new RenameFileForm();
        $model->setGateway($gateway);
        $model->setFile($file);

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                return [
                    'status' => 'success',
                    'redirect' => Url::toRoute(['settings/files', 'id' => $file->id]),
                    'message' => Yii::t('admin', 'settings.files_message_updated'),
                ];
            }

            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model),
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('admin', 'settings.files_error'),
        ];
    }

    /**
     * Create file
     * @return array
     */
    public function actionCreateFile()
    {
        $gateway = Yii::$app->gateway->getInstance();

        $model = new CreateFileForm();
        $model->setGateway($gateway);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return [
                    'status' => 'success',
                    'redirect' => Url::toRoute(['settings/files', 'id' => $model->id]),
                    'message' => Yii::t('admin', 'settings.files_message_created'),
                ];
            }

            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model),
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('admin', 'settings.files_error'),
        ];
    }

    /**
     * Upload file
     * @return array
     */
    public function actionUploadFile()
    {
        $gateway = Yii::$app->gateway->getInstance();

        $model = new UploadFileForm();
        $model->setGateway($gateway);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return [
                    'status' => 'success',
                    'redirect' => Url::toRoute(['settings/files', 'id' => $model->id]),
                    'message' => Yii::t('admin', 'settings.files_message_created'),
                ];
            }

            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model),
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('admin', 'settings.files_error'),
        ];
    }

    /**
     * Delete file by file id
     * @param integer $id
     * @return array
     */
    public function actionDeleteFile($id)
    {
        /**
         * @var Files $file
         */
        $file = $this->_findModel($id, Files::class);

        if (!Files::can(Files::CAN_DELETE, $file)) {
            return [
                'status' => 'error',
                'message' => Yii::t('admin', 'settings.files_error'),
            ];
        }

        if ($file->delete()) {
            return [
                'status' => 'success',
                'redirect' => Url::toRoute(['settings/files']),
                'message' => Yii::t('admin', 'settings.files_message_deleted'),
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('admin', 'settings.files_error'),
        ];
    }

    /**
     * Preview file by file id
     * @param integer $id
     */
    public function actionPreviewFile($id)
    {
        /**
         * @var Files $file
         */
        $file = $this->_findModel($id, Files::class);

        if (!Files::can(Files::CAN_PREVIEW, $file)) {
            throw new ForbiddenHttpException();
        }

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', $file->mime);
        $response->data = $file->content;
        return $response;
    }
}