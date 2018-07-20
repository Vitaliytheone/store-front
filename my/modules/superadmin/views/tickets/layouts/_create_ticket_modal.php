<?php
    /* @var $this yii\web\View */
    /* @var $model \my\modules\superadmin\models\forms\CreateTicketForm */
    /* @var $ticket \common\models\panels\Tickets */

    use my\modules\superadmin\models\forms\CreateTicketForm;
    use my\helpers\Url;
    use my\components\ActiveForm;
    use yii\bootstrap\Html;
    use my\modules\superadmin\widgets\SelectCustomer;

    $model = new CreateTicketForm();
    $this->context->addModule('superadminSelectCustomerController');
?>

<div class="modal fade" id="create-ticket" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'tickets.create.modal_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app/superadmin', 'tickets.modal.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'createTicketForm',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                    'labelOptions' => ['class' => 'control-label'],
                ],
            ]); ?>
            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'createTicketError'
                ]); ?>

                <div class="form-group">
                    <label><?= Yii::t('app/superadmin', 'tickets.create.column_customer') ?> </label>
                    <div>
                        <?= SelectCustomer::widget([
                            'models' => $model->getCustomers(),
                            'context' => $this->context,
                            'name' => 'CreateTicketForm[customer_id]',
                            'selectedCustomerId' => $model->customer_id,
                        ]) ?>
                    </div>
                </div>

                <?= $form->field($model, 'subject') ?>

                <?= $form->field($model, 'message')->textarea([
                    'rows' => 5
                ]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'tickets.btn.close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'tickets.btn.submit'), ['class' => 'btn  btn-primary', 'name' => 'save-button', 'id' => 'createTicketButton']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>