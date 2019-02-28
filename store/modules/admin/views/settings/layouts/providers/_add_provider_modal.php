<?php
    /* @var $this yii\web\View */
    /* @var $form \common\components\ActiveForm */

    use common\components\ActiveForm;
    use yii\bootstrap\Html;
    use store\modules\admin\components\Url;

    $model = new \store\modules\admin\models\forms\CreateProviderForm();
?>
<div class="modal fade add_provider" id="createProviderModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'settings.providers_m_title') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'createProviderForm',
                'action' => Url::toRoute('/settings/create-provider'),
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
                    'id' => 'createProviderError'
                ]); ?>

                <?= $form->field($model, 'name') ?>
            </div>
            <div class="modal-footer justify-content-start">
                <?= Html::submitButton(Yii::t('admin', 'settings.providers_m_add'), [
                    'class' => 'btn btn-primary m-btn--air',
                    'name' => 'create-provider-button',
                    'id' => 'createProviderButton'
                ]) ?>
                <button type="button" class="btn btn-secondary m-btn--air" data-dismiss="modal">
                    <?= Yii::t('admin', 'settings.providers_m_cancel') ?>
                </button>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>