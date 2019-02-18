<?php
    /* @var $this yii\web\View */
    /* @var $form \my\components\ActiveForm */
    /* @var $model \superadmin\models\forms\ChangeStoreDomainForm */

    use my\components\ActiveForm;
    use yii\bootstrap\Html;
    use superadmin\models\forms\EditStoreForm;
    use sommerce\helpers\ConfigHelper;
    use superadmin\widgets\SelectCustomer;

    $model = new EditStoreForm();
    $checkboxTemplate = "<div class=\"custom-control custom-checkbox mt-2\">{input} {label}</div>";
?>
<div class="modal fade" id="editStoreModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'stores.modal.edit_store_title')?></h5>
                <button type="button" class="close" id="editstoreform-x_close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'editStoreForm',
                'options' => [
                    'class' => "form",
                ],
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>
            <div class="modal-body">
                <div class="form-group">
                    <?= $form->errorSummary($model, [
                        'id' => 'editStoreError'
                    ]); ?>
                    <?= $form->field($model, 'name') ?>
                </div>
                <div class="form-group">
                    <label><?= $model->getAttributeLabel('customer_id') ?></label>
                    <div>
                        <?= SelectCustomer::widget([
                                'context' => $this->context,
                                'name' => 'EditStoreForm[customer_id]',
                                'selectedCustomerId' => $model->customer_id,
                                'status' => 'all',
                        ]) ?>
                    </div>
                </div>
                <?= $form->field($model, 'move_domain', [
                    'checkboxTemplate' => $checkboxTemplate,
                    'options' => ['class' => 'form-group move-domain-block'],
                ])->checkbox([
                    'class' => 'custom-control-input', 'id' => 'move-domain',
                ])->label(null,[
                    'class' => 'custom-control-label'
                ]); ?>
                <div class="form-group">
                    <label><?= $model->getAttributeLabel('currency') ?></label>
                    <?= Html::dropDownList('EditStoreForm[currency]',
                        '',
                        ConfigHelper::getCurrenciesList(),
                        ['class' => 'form-control', 'id' => 'editstoreform-currency_option']) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="editstoreform-close" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'stores.btn.modal_close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'stores.btn.submit'), [
                    'class' => 'btn btn-primary',
                    'name' => 'edit-store-button',
                    'id' => 'editStoreButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
