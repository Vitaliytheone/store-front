<?php
/* @var $this yii\web\View */
/* @var $form \my\components\ActiveForm */
/* @var $modal \superadmin\models\forms\EditGatewayExpiryForm */

use my\components\ActiveForm;
use my\helpers\Url;
use yii\bootstrap\Html;
use superadmin\widgets\DateTimePicker;

$model = new \superadmin\models\forms\EditGatewayExpiryForm();
?>
<div class="modal fade" id="editExpiryModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'gateways.edit.expiry')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app/superadmin', 'gateways.edit.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'editExpiryForm',
                'action' => Url::toRoute("/gateways/edit-expiry"),
                'options' => [
                    'class' => "form",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
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
                        'context' => $this->context,
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'gateways.edit.close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'gateways.edit.save'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-expiry-button',
                    'id' => 'editExpiryButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>