<?php
    /* @var $this yii\web\View */
    /* @var $form \my\components\ActiveForm */
    /* @var $modal \superadmin\models\forms\ChangeStoreDomainForm */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new superadmin\models\forms\ChangeStoreDomainForm();
?>
<div class="modal fade" id="changeDomainModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'stores.modal.change_domain_modal_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'changeDomainForm',
                'action' => Url::toRoute('/stores/change-domain'),
                'options' => [
                    'class' => "form",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                    'options' => ['tag' => false],
                ],
            ]); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <?= $form->errorSummary($model, [
                            'id' => 'changeDomainError'
                        ]); ?>

                        <?= $form->field($model, 'domain') ?>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" id="form-subdomain" class="custom-control-input">
                            <label class="custom-control-label" for="form-subdomain"><?= $model->getAttributeLabel('subdomain') ?></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'stores.btn.modal_close') ?></button>
                    <?= Html::submitButton(Yii::t('app/superadmin', 'stores.btn.submit'), [
                        'class' => 'btn btn-primary',
                        'name' => 'change-domain-button',
                        'id' => 'changeDomainButton'
                    ]) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>