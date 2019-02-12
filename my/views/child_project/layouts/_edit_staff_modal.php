<?php
    /* @var $this yii\web\View */
    /* @var $model \my\models\forms\EditStaffForm */

    use my\models\forms\EditStaffForm;
    use my\components\ActiveForm;
    use common\models\panels\ProjectAdmin;
    use yii\bootstrap\Html;

    $model = new EditStaffForm();
?>
<div class="modal fade" id="editStaffModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'panels.edit_staff.header')?></h4>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'editStaffForm',
                'fieldConfig' => [
                    'template' => "{label}{input}",
                ],
                'options' => [
                    'class' => 'form'
                ]
            ]); ?>
                <div class="modal-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'editStaffError'
                    ]); ?>

                    <?= $form->field($model, 'account') ?>

                    <?= $form->field($model, 'status')->dropDownList(ProjectAdmin::getStatuses()) ?>

                    <div class="form-group">
                        <label for="">Access</label><br>
                        <?php foreach ($model->getAccessRules() as $code => $label) : ?>
                            <?php if ('providers' == $code) continue; ?>
                            <label class="checkbox-inline">
                                <?= Html::checkbox('EditStaffForm[access][' . $code . ']', true, ['class' => 'access'])?>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group" id="wrap-settings">
                        <?php foreach ($model->getAccessSettings() as $code => $label) : ?>
                            <?php if ('providers' == $code) continue; ?>
                            <label class="checkbox-inline">
                                <?= Html::checkbox('EditStaffForm[access][' . $code . ']', true, ['class' => 'access settings'])?>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group" id="wrap-appearance">
                        <?php foreach ($model->getAccessAppearance() as $code => $label) : ?>
                            <?php if ('providers' == $code) continue; ?>
                            <label class="checkbox-inline">
                                <?= Html::checkbox('EditStaffForm[access][' . $code . ']', true, ['class' => 'access appearance'])?>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label for="hide-providers"><?= Yii::t('app', 'form.edit_staff.rules_providers')?></label>
                        <div class="form-group">
                            <label class="switch">
                                <?= Html::checkbox('EditStaffForm[access][providers]', true, ['id' => 'hide-providers', 'class' => 'access'])?>
                                <span class="switch-slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'panels.edit_staff.modal_cancel')?></button>
                    <?= Html::submitButton(Yii::t('app', 'panels.edit_staff.modal_submit'), [
                        'class' => 'btn btn-outline btn-primary',
                        'name' => 'edit-staff-button',
                        'id' => 'editStaffButton'
                    ]) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>