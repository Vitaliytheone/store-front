<?php
/* @var $this yii\web\View */
/* @var $form \my\components\ActiveForm */
/* @var $modal \my\modules\superadmin\models\forms\DowngradePanelForm */

use my\modules\superadmin\models\forms\DowngradePanelForm;
use my\components\ActiveForm;
use my\helpers\Url;
use yii\bootstrap\Html;

$model = new DowngradePanelForm();
?>
<div class="modal fade" id="downgradePanelModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'panels.downgrade.header'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app/superadmin', 'panels.edit.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'downgradePanelForm',
                'action' => Url::toRoute('/panels/downgrade'),
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
                    'id' => 'downgradePanelError'
                ]); ?>

                <?= $form->field($model, 'provider')->dropDownList([], [
                    'id' => 'providers'
                ]) ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'panels.downgrade.cancel_btn'); ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'panels.downgrade.submit_btn'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'downgrade-panel-button',
                    'id' => 'downgradePanelButton',
                    'data-title' => Yii::t('app/superadmin', 'panels.downgrade.confirm')
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>