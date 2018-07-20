<?php
    /* @var $this yii\web\View */
    /* @var $form \my\components\ActiveForm */
    /* @var $modal \my\modules\superadmin\models\forms\UpgradePanelForm */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;
    use my\modules\superadmin\models\forms\UpgradePanelForm;

    $model = new UpgradePanelForm();
?>
<div class="modal fade confirm-modal" id="upgradePanelModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'panels.upgrade.header'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'upgradePanelForm',
                'action' => Url::toRoute('/child-panels/upgrade'),
                'options' => [
                    'class' => "form",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>
            <div class="modal-body max-height-400">
                <?= $form->errorSummary($model, [
                    'id' => 'upgradePanelError'
                ]); ?>
                <div class="hidden">
                    <?= Html::activeCheckbox($model, 'mode')?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'panels.upgrade.cancel_btn'); ?></button>

                <?= Html::submitButton(Yii::t('app/superadmin', 'child_panels.upgrade.submit_not_invoice'), [
                    'class' => 'btn btn-outline btn-primary upgrade-panel-button',
                    'name' => 'upgrade-panel-button',
                    'data-mode' => 0
                ]) ?>

                <?= Html::submitButton(Yii::t('app/superadmin', 'child_panels.upgrade.submit_invoice', [
                    'price' => '$' . $model->getTotal()
                ]), [
                    'class' => 'btn btn-outline btn-primary upgrade-panel-button',
                    'name' => 'upgrade-panel-button',
                    'data-mode' => 1
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>