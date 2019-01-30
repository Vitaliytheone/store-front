<?php
namespace admin\controllers\traits\settings;

use admin\models\forms\CreateFileForm;
use admin\models\forms\EditFileForm;
use admin\models\forms\RenameFileForm;
use admin\models\search\FilesSearch;
use common\components\ActiveForm;
use common\models\gateway\Files;
use gateway\controllers\CommonController;
use gateway\helpers\FilesHelper;
use Yii;
use yii\web\NotFoundHttpException;

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
                    'filename' => $file->name,
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
                    'filename' => $model->name,
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
                    'filename' => $model->name,
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
                'message' => Yii::t('admin', 'settings.files_message_deleted'),
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('admin', 'settings.files_error'),
        ];
    }
}