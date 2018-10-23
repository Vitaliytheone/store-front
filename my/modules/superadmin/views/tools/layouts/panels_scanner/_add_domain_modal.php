<?php

    use my\components\ActiveForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new \superadmin\models\forms\PanelsScannerAddDomainForm();

    /* @var $this yii\web\View */
    /* @var $model \superadmin\models\forms\PanelsScannerAddDomainForm */
    /* @var $panelType string */
?>

<div class="modal fade" id="add_domain_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?= Yii::t('app/superadmin', 'tools.levopanel.modal.title') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'addDomainForm',
                'action' => Url::toRoute(['/tools/add-domain', 'panel' => $panelType]),
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
                    'id' => 'addDomainError'
                ]); ?>
                <?= $form->field($model, 'domain') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?= Yii::t('app/superadmin', 'tools.levopanel.modal.button.cancel') ?>
                </button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'tools.levopanel.modal.button.add'),[
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-provider-button',
                    'id' => 'addDomainButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>