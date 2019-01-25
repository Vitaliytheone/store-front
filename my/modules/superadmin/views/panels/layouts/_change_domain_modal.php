<?php
    /* @var $this yii\web\View */
    /* @var $form \my\components\ActiveForm */
    /* @var $action string */
    /* @var $modal \superadmin\models\forms\ChangeDomainForm */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new superadmin\models\forms\ChangeDomainForm();
?>
<div class="modal fade" id="changeDomainModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'panels.edit.change_domain') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'changeDomainForm',
                'action' => Url::toRoute("/$action/change-domain"),
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
                        'id' => 'changeDomainError'
                    ]); ?>

                    <?= $form->field($model, 'domain') ?>

                    <?= $form->field($model, 'subdomain')->checkbox() ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'panels.edit.close') ?></button>
                    <?= Html::submitButton(Yii::t('app/superadmin', 'panels.edit.save'), [
                        'class' => 'btn btn-outline btn-primary',
                        'name' => 'change-domain-button',
                        'id' => 'changeDomainButton'
                    ]) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>