<?php
/* @var $this yii\web\View */
/* @var $form \gateway\components\ActiveForm */
/* @var $modal admin\models\forms\RenameFileForm */

use admin\models\forms\RenameFileForm;
use gateway\components\ActiveForm;
use my\helpers\Url;
use yii\bootstrap\Html;

$model = new RenameFileForm();
?>
<div class="modal fade" id="renameFileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'id' => 'renameFileForm',
                'action' => Url::toRoute("/settings/rename-file"),
                'options' => [
                    'class' => "modal-content",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>
                <div class="modal-body">

                    <?= $form->field($model, 'name') ?>

                    <div class="text-right">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"><?= Yii::t('admin', 'settings.files.rename_file.cancel') ?></button>

                        <?= Html::submitButton(Yii::t('admin', 'settings.files.rename_file.save'), [
                            'class' => 'btn btn-primary',
                            'name' => 'rename-file-button',
                            'id' => 'renameFileButton'
                        ]) ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>