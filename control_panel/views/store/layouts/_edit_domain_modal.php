<?php
    /* @var $this yii\web\View */
    /* @var $model \control_panel\models\forms\EditStoreDomainForm */

    use control_panel\models\forms\EditStoreDomainForm;
    use common\models\sommerces\Content;
    use control_panel\components\ActiveForm;
    use yii\bootstrap\Html;

    $model = new EditStoreDomainForm();
    $content = Content::getContent('store_nameservers');
?>

<div class="modal fade" id="editStoreDomainModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'stores.edit_store_domain.header')?></h4>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'editStoreDomainForm',
                'fieldConfig' => [
                    'template' => "{label}{input}",
                ],
                'options' => [
                    'class' => 'form'
                ]
            ]); ?>
                <div class="modal-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'editStoreDomainError'
                    ]); ?>

                    <?= $form->field($model, 'domain', [
                        'template' => "<div class=\"form-group\">\n<label>{label}</label>\n{input}\n<div id=\"helpDomain\" class=\"alert-help\">\n<div class=\"alert alert-info\">\n{$content}\n</div>\n</div>\n</div>"
                    ])->textInput() ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline btn-default" data-dismiss="modal"><?= Yii::t('app', 'stores.edit_store_domain.modal_cancel')?></button>
                    <?= Html::submitButton(Yii::t('app', 'stores.edit_store_domain.modal_submit'), [
                        'class' => 'btn btn-outline btn-primary',
                        'name' => 'btn btn-outline btn-primary',
                        'id' => 'editStoreDomainButton'
                    ]) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>