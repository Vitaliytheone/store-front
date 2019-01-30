<?php
/* @var $this yii\web\View */
/* @var $model EditApplicationsForm */

/* @var $form my\components\ActiveForm */

use my\components\ActiveForm;
use superadmin\models\forms\EditApplicationsForm;
use my\helpers\Url;
use yii\bootstrap\Html;

$model = new EditApplicationsForm();
?>

<div class="modal fade" id="editApplicationsModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'applications.edit.modal_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editApplicationsForm',
                'action' => Url::toRoute('/settings/edit-applications'),
                'options' => [
                    'class' => 'form',
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>

            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'editApplicationsError'
                ]); ?>

                <?= $form->field($model, 'code')->textInput([
                    'disabled' => 'disabled'
                ]) ?>

                <?= $form->field($model, 'options')->textarea([
                    'rows' => 8
                ]) ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light"
                        data-dismiss="modal"><?= Yii::t('app/superadmin', 'applications.edit.modal_cancel_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'applications.edit.modal_submit_btn'), [
                    'class' => 'btn btn-primary',
                    'name' => 'edit-plan-button',
                    'id' => 'editApplicationsButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>