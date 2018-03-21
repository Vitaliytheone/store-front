<?php
/* @var $this yii\web\View */
/* @var $model my\modules\superadmin\models\forms\EditStaffForm */
/* @var $form my\components\ActiveForm */

use my\components\ActiveForm;
use my\modules\superadmin\models\forms\EditStaffForm;
use my\helpers\Url;
use common\models\panels\SuperAdmin;
use yii\bootstrap\Html;

$model = new EditStaffForm();
?>

<div class="modal fade" id="editStaffModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit account</h4>
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <?= Html::submitButton('Edit account', [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-staff-button',
                    'id' => 'editStaffButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>