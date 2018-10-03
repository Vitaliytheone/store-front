<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\CreatePlanForm */
    /* @var $form my\components\ActiveForm */

    use my\components\ActiveForm;
    use my\modules\superadmin\models\forms\CreatePlanForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new CreatePlanForm();
?>

<div class="modal fade" id="createPlanModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'plan.create.modal_header')?></h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'createPlanForm',
                'action' => Url::toRoute('/settings/create-plan'),
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
                        'id' => 'createPlanError'
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
                    <button type="button" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'plan.create.modal_cancel_btn')?></button>
                    <?= Html::submitButton(Yii::t('app/superadmin', 'plan.create.modal_submit_btn'), [
                        'class' => 'btn btn-outline btn-primary',
                        'name' => 'create-plan-button',
                        'id' => 'createPlanButton'
                    ]) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>