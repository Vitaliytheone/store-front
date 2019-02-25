<?php
/* @var $this yii\web\View */
/* @var $form \control_panel\components\ActiveForm */
/* @var $modal \superadmin\models\forms\EditProvidersForm */
/* @var $action string */

use control_panel\components\ActiveForm;
use control_panel\helpers\Url;
use yii\bootstrap\Html;

$model = new \superadmin\models\forms\ChangeChildPanelProvider();
?>
<div class="modal fade" id="changeChildPanelProviderModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'panels.change_provider.header') ?></h5>
                <button type="button" class="close close-change-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'changeChildPanelProviderForm',
                'action' => Url::toRoute("/child-panels/change-provider"),
                'options' => [
                    'class' => "form",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>

            <?= $form->errorSummary($model, [
                'id' => 'changeChildPanelProviderError'
            ]); ?>

            <div class="modal-body">
                <?= $form->field($model, 'provider', ['options' => [
                    'id' => 'form-providers',
                    'class' => 'form-group providers-list']])
                    ->dropDownList([]) ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light close-change-modal" data-dismiss="modal"><?= Yii::t('app/superadmin', 'panels.change_provider.close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'panels.change_provider.save'), [
                    'class' => 'btn  btn-primary',
                    'name' => 'edit-providers-button',
                    'id' => 'changeChildPanelProviderButton',
                    'data-dismiss' => 'modal'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>