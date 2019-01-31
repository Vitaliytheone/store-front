<?php
/* @var $this yii\web\View */
/* @var $form \gateway\components\ActiveForm */
/* @var $modal admin\models\forms\CreateFileForm */

use admin\models\forms\UploadFileForm;
use gateway\components\ActiveForm;
use my\helpers\Url;
use yii\bootstrap\Html;

$model = new UploadFileForm();
?>

<div class="modal fade" id="uploadFileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'id' => 'uploadFileForm',
                'action' => Url::toRoute("/settings/upload-file"),
                'options' => [
                    'class' => "modal-content",
                    'enctype' => 'multipart/form-data',
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>
            <div class="modal-content">
                <div class="modal-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'uploadFileError'
                    ]); ?>

                    <div class="mb-4">
                        <div class="form-group">
                            <div>
                                <?= Html::activeLabel($model, 'file')?>
                            </div>
                            <?= Html::activeFileInput($model, 'file')?>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"><?= Yii::t('admin', 'settings.files.upload_file.cancel') ?></button>
                        <?= Html::submitButton(Yii::t('admin', 'settings.files.upload_file.upload'), [
                            'class' => 'btn btn-primary',
                            'name' => 'upload-file-button',
                            'id' => 'uploadFileButton'
                        ]) ?>
                    </div>

                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>