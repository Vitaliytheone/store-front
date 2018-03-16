<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\EditPlanForm */
    /* @var $form my\components\ActiveForm */
    
    use my\components\ActiveForm;
    use my\modules\superadmin\models\forms\EditPlanForm;
    use my\helpers\Url;
    use common\models\panels\SuperAdmin;
    use yii\bootstrap\Html;

    $model = new EditPlanForm();
?>

<div class="modal fade" id="editPlanModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= Yii::t('app/superadmin', 'plan.edit.modal_header')?></h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editPlanForm',
                'action' => Url::toRoute('/settings/edit-plan'),
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
                    'id' => 'editPlanError'
                ]); ?>

                <?= $form->field($model, 'title') ?>

                <?= $form->field($model, 'price') ?>

                <?= $form->field($model, 'description')->textarea() ?>

                <?= $form->field($model, 'of_orders') ?>

                <?= $form->field($model, 'before_orders') ?>

                <?= $form->field($model, 'up') ?>

                <?= $form->field($model, 'down') ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'plan.edit.modal_cancel_btn')?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'plan.edit.modal_submit_btn'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-plan-button',
                    'id' => 'editPlanButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>