<?php
    /* @var $this yii\web\View */
    /* @var $model \my\modules\superadmin\models\forms\CreateTicketForm */
    /* @var $ticket \common\models\panels\Tickets */

    use my\modules\superadmin\models\forms\CreateTicketForm;
    use my\helpers\Url;
    use yii\helpers\ArrayHelper;
    use my\components\ActiveForm;
    use yii\bootstrap\Html;

    $model = new CreateTicketForm();
?>

<div class="modal fade" id="createTicketModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'tickets.create.modal_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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


                <div class="form-group field-editprojectform-cid">
                    <label class="control-label" for="createticketform-customer_id"><?= $model->getAttributeLabel('customer_id')?></label>
                    <select id="createticketform-customer_id" class="form-control selectpicker" name="CreateTicketForm[customer_id]" data-live-search="true">
                        <?php foreach ($model->getCustomers() as $customer) : ?>
                            <option data-tokens="<?= $customer->email ?>" value="<?= $customer->id ?>" <?= ($customer->id == $model->customer_id ? 'selected' : '') ?>>
                                <?= $customer->email ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?= $form->field($model, 'subject') ?>

                <?= $form->field($model, 'message')->textarea([
                    'rows' => 5
                ]) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton(Yii::t('app/superadmin', 'tickets.btn.submit'), ['class' => 'btn btn-primary', 'name' => 'save-button', 'id' => 'createTicketButton']) ?>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('app/superadmin', 'tickets.btn.close') ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>