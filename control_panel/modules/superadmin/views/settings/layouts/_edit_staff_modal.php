<?php
/* @var $this yii\web\View */
/* @var $model superadmin\models\forms\EditStaffForm */
/* @var $form control_panel\components\ActiveForm */

use control_panel\components\ActiveForm;
use superadmin\models\forms\EditStaffForm;
use control_panel\helpers\Url;
use common\models\panels\SuperAdmin;
use yii\bootstrap\Html;

$model = new EditStaffForm();
?>

<div class="modal fade" id="editStaffModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= Yii::t('app/superadmin', 'staff.edit_staff.modal_header') ?></h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editStaffForm',
                'action' => Url::toRoute('/settings/edit-staff'),
                'options' => [
                    'class' => "form",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>

            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'editStaffError'
                ]); ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'first_name') ?>

                <?= $form->field($model, 'last_name') ?>

                <?= $form->field($model, 'status')->dropDownList(SuperAdmin::getStatuses()) ?>

                <div class="form-group">
                    <label for=""><?= $model->getAttributeLabel('access') ?></label><br>
                    <?php foreach (SuperAdmin::getRulesLabels() as $code => $label) : ?>
                        <label class="checkbox-inline">
                            <?= Html::checkbox('EditStaffForm[access][' . $code . ']', true, ['class' => 'access'])?>
                            <?= $label ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'staff.edit_staff.modal_cancel_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'staff.edit_staff.modal_edit_account'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-staff-button',
                    'id' => 'editStaffButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>