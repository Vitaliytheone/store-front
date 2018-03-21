<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\CreateStaffForm */
    /* @var $form my\components\ActiveForm */

    use my\components\ActiveForm;
    use my\modules\superadmin\models\forms\CreateStaffForm;
    use my\helpers\Url;
    use common\models\panels\SuperAdmin;
    use yii\bootstrap\Html;

    $model = new CreateStaffForm();
?>

<div class="modal fade" id="createStaffModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create account</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'createStaffForm',
                'action' => Url::toRoute('/settings/create-staff'),
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
                        'id' => 'createStaffError'
                    ]); ?>

                    <?= $form->field($model, 'username') ?>

                    <div class="form-group">
                        <label for=""><?= $model->getAttributeLabel('password') ?></label>
                        <div class="input-group">
                            <?= Html::textInput('CreateStaffForm[password]', '', ['class' => 'form-control password'])?>
                            <span class="input-group-addon">
                                <span class="btn btn-default random-password pointer">
                                    <i class="fa fa-random fa-fw" data-toggle="tooltip" data-placement="right" title="Generate password"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <?= $form->field($model, 'first_name') ?>

                    <?= $form->field($model, 'last_name') ?>

                    <?= $form->field($model, 'status')->dropDownList(SuperAdmin::getStatuses()) ?>

                    <div class="form-group">
                        <label for=""><?= $model->getAttributeLabel('access') ?></label><br>
                        <?php foreach (SuperAdmin::getRulesLabels() as $code => $label) : ?>
                            <label class="checkbox-inline">
                                <?= Html::checkbox('CreateStaffForm[access][' . $code . ']', true, ['class' => 'access'])?>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <?= Html::submitButton('Create account', [
                        'class' => 'btn btn-outline btn-primary',
                        'name' => 'create-staff-button',
                        'id' => 'createStaffButton'
                    ]) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>