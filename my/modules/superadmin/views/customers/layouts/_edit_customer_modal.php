<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\EditCustomerForm */
    /* @var $form my\components\ActiveForm */

    use my\components\ActiveForm;
    use my\modules\superadmin\models\forms\EditCustomerForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new EditCustomerForm();
?>

<div class="modal fade" id="editCustomerModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit customer</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editCustomerForm',
                'action' => Url::toRoute('/customers/edit'),
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
                    'id' => 'editCustomerError'
                ]); ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'first_name') ?>

                <?= $form->field($model, 'last_name') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <?= Html::submitButton('Edit customer', [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-customer-button',
                    'id' => 'editCustomerButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>