<?php
    /* @var $this yii\web\View */
    /* @var $panel \common\models\panels\Project */
    /* @var $model \control_panel\models\forms\CreateStaffForm */

    use control_panel\models\forms\CreateStaffForm;
    use control_panel\components\ActiveForm;
    use common\models\panels\ProjectAdmin;
    use yii\bootstrap\Html;
    use control_panel\helpers\Url;

    $model = new CreateStaffForm();
    $wrappedRules = $model->getWrappedRules();
?>
<div class="modal fade" id="createStaffModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'panels.create_staff.header')?></h4>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'createStaffForm',
                'action' => Url::toRoute('/staff/create/' . $panel->id),
                'fieldConfig' => [
                    'template' => "{label}{input}",
                ],
                'options' => [
                    'class' => 'form'
                ]
            ]); ?>
                <div class="modal-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'createStaffError'
                    ]); ?>

                    <?= $form->field($model, 'account') ?>

                    <div class="form-group">
                        <label for=""><?= $model->getAttributeLabel('password') ?></label>
                        <div class="input-group">
                            <?= Html::textInput('CreateStaffForm[password]', '', ['class' => 'form-control password'])?>
                            <span class="input-group-btn random-password">
                                <button class="btn btn-default" type="button" id="staff_edit_gen"><i class="fa fa-random fa-fw" data-toggle="tooltip" data-placement="right" title="Generate password"></i></button>
                            </span>
                        </div>
                    </div>

                    <?= $form->field($model, 'status')->dropDownList(ProjectAdmin::getStatuses()) ?>

                    <div class="form-group">
                        <label for=""><?= Yii::t('app', 'project_admin.access_label') ?></label><br>
                        <?php foreach ($model->getAccessRules() as $code => $label) : ?>
                            <?php if ('providers' == $code) continue; ?>
                            <?php $customClass = array_key_exists($code, $wrappedRules) ? $wrappedRules[$code] : '' ?>
                            <label class="checkbox-inline">
                                <?= Html::checkbox('CreateStaffForm[access][' . $code . ']', true, ['class' => 'access ' . $customClass])?>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label for="hide-providers"><?= Yii::t('app', 'project_admin.rules_providers')?></label>
                        <div class="form-group">
                            <label class="switch">
                                <?= Html::checkbox('CreateStaffForm[access][providers]', false, ['id' => 'hide-providers', 'class' => 'access'])?>
                                <span class="switch-slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'panels.create_staff.modal_cancel')?></button>
                    <?= Html::submitButton(Yii::t('app', 'panels.create_staff.modal_submit'), [
                        'class' => 'btn btn-outline btn-primary',
                        'name' => 'create-staff-button',
                        'id' => 'createStaffButton'
                    ]) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>