<?php
    /* @var $this yii\web\View */
    /* @var $form \my\components\ActiveForm */
    /* @var $modal \my\modules\superadmin\models\forms\EditExpiryForm */
    /* @var $action string */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new \my\modules\superadmin\models\forms\EditExpiryForm();
?>
<div class="modal fade" id="editExpiryModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'panels.edit.expiry')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app/superadmin', 'panels.edit.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'editExpiryForm',
                'action' => Url::toRoute("/$action/edit-expiry"),
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
                    <div class="input-group date" id="expired-time" data-target-input="nearest">
                        <?= $form->field($model, 'expired', [
                            'options' => [
                                'tag' => false,
                            ]
                        ])->textInput([
                            'class' => 'form-control datetimepicker-input',
                            'data-target' => '#expired-time'
                        ])->label(false) ?>
                        <div class="input-group-append" data-target="#expired-time" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'panels.edit.close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'panels.edit.save'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-expiry-button',
                    'id' => 'editExpiryButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>