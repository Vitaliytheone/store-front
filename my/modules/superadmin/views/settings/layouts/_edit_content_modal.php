<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\EditContentForm */
    /* @var $form my\components\ActiveForm */
    
    use my\components\ActiveForm;
    use my\modules\superadmin\models\forms\EditContentForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new EditContentForm();
?>

<div class="modal fade" id="editContentModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'content.edit.modal_header')?></h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editContentForm',
                'action' => Url::toRoute('/settings/edit-content'),
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
                    'id' => 'editContentError'
                ]); ?>

                <?= $form->field($model, 'name')->textInput([
                    'disabled' => 'disabled'
                ]) ?>

                <?= $form->field($model, 'text')->textarea([
                    'rows' => 10
                ]) ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'content.edit.modal_cancel_btn')?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'content.edit.modal_submit_btn'), [
                    'class' => 'btn btn-primary',
                    'name' => 'edit-plan-button',
                    'id' => 'editContentButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>