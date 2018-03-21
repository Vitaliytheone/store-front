<?php
    /* @var $this yii\web\View */
    /* @var $form \my\components\ActiveForm */
    /* @var $modal \my\modules\superadmin\models\forms\EditProvidersForm */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new \my\modules\superadmin\models\forms\EditProvidersForm();
?>
<div class="modal fade" id="editProvidersModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit providers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'editProvidersForm',
                'action' => Url::toRoute('/panels/edit-providers'),
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
                    'id' => 'editProvidersError'
                ]); ?>

                <?= $form->field($model, 'providers')->checkboxList($model->getProviders())->label(false) ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <?= Html::submitButton('Save changes', [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-providers-button',
                    'id' => 'editProvidersButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>