<?php
    /* @var $this yii\web\View */
    /* @var $form \my\components\ActiveForm */
    /* @var $modal \my\modules\superadmin\models\forms\EditStoreExpiryForm */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;
    use my\modules\superadmin\widgets\DateTimePicker;

    $model = new \my\modules\superadmin\models\forms\EditStoreExpiryForm();
?>
<div class="modal fade" id="editExpiryModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'stores.modal.edit_expire_modal_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'editExpiryForm',
                'action' => Url::toRoute('/stores/edit-expiry'),
                'options' => [
                    'class' => "form",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{input}",
                    'options' => [
                        'tag' => false,
                    ],
                ],
            ]); ?>
            <div class="modal-body">
                <div class="form-group">
                    <?= $form->errorSummary($model, [
                        'id' => 'editExpiryError'
                    ]); ?>
                    <?= DateTimePicker::widget([
                            'model' => $model,
                            'attribute' => 'expired',
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'stores.btn.modal_close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'stores.btn.submit'), [
                    'class' => 'btn btn-primary',
                    'name' => 'edit-expiry-button',
                    'id' => 'editExpiryButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>