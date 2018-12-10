<?php
/* @var $action string */

use my\components\ActiveForm;
use my\helpers\Url;
use yii\bootstrap\Html;
use superadmin\models\forms\EditProjectForm;
use yii\helpers\Json;
use superadmin\widgets\SelectCustomer;

$model = new EditProjectForm();
$checkboxTemplate = "<div class=\"custom-control custom-checkbox mt-2\">{input} {label}</div>";
$checkboxTemplateGroup = "<div class=\"custom-control custom-checkbox custom-checkbox-filter\">{input} {label}</div>";
?>

<div class="modal fade" id="editPanelsModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'panels.list.details') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'edit-panel-form',
                'options' => [
                    'class' => "form",
                    "data-inputs" => Json::encode($model->getInputs())
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ]
            ]);?>
            <div class="modal-body">
                <?= $form->errorSummary($model, [
                'id' => 'edit-panel-error'
                ]); ?>
                <?= $form->field($model, 'site')->textInput([
                    'readonly' => true,
                    'id' => 'editprojectform-site',
                    'value' => ""
                ]) ?>
                <?= $form->field($model, 'subdomain', [
                    'checkboxTemplate' => $checkboxTemplate,
                ])->checkbox([
                    'class' => 'custom-control-input', 'id' => 'form-subdomain',
                ])->label(null,[
                    'class' => 'custom-control-label'
                ]); ?>

                <?= $form->field($model, 'affiliate_system', [
                    'checkboxTemplate' => $checkboxTemplate,
                ])->checkbox([
                    'class' => 'custom-control-input', 'id' => 'form-affiliate_system',
                ])->label(null,[
                    'class' => 'custom-control-label'
                ]); ?>

                <?= $form->field($model, 'name', ['options' => ['id' => 'form-name', 'class' => 'form-group']])->label(Yii::t('app/superadmin', 'panels.edit.panel_name')) ?>
                <?= $form->field($model, 'skype', ['options' =>['id' => 'form-skype', 'class' => 'form-group']]) ?>

                <?php if ($action == 'panels') : ?>
                    <?= $form->field($model, 'plan', ['options' => ['id' => 'form-plan', 'class' => 'form-group']])->dropDownList($model->getPlans(), [
                        'class' => 'selectpicker w-100',
                        'data-live-search' => 'true'
                    ]) ?>
                <?php endif; ?>

                <div class="form-group field-editprojectform-cid">
                    <label class="control-label" for="editprojectform-cid"><?= $model->getAttributeLabel('cid')?></label>
                    <?= SelectCustomer::widget([
                        'context' => $this->context,
                        'name' => 'EditProjectForm[cid]',
                    ]) ?>
                </div>

                <?= $form->field($model, 'currency_code', ['options' => [
                    'id' => 'form-currency',
                    'class' => 'form-group']])
                    ->dropDownList($model->getCurrencies()) ?>

                <?= $form->field($model, 'utc', ['options' => [
                    'id' => 'form-utc',
                    'class' => 'form-group']])
                    ->dropDownList($model->getTimezones()) ?>

                <div class="card-custom">
                    <div class="card-custom__title"><?= Yii::t('app/superadmin', 'panels.edit.service_type_header') ?></div>
                    <div class="form-group">
                        <div class="d-flex flex-wrap mb-1">
                            <?php foreach($model->getServiceTypes() as $serviceType) : ?>
                                <?= $form->field($model, $serviceType, [
                                    'checkboxTemplate' => $checkboxTemplateGroup, 'options' => ['tag' => false]])
                                    ->checkbox(['class' => 'custom-control-input','id' => 'form-' . $serviceType])
                                    ->label(null, ['class' => 'custom-control-label'])
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="card-custom">
                    <div class="card-custom__title"><?= Yii::t('app/superadmin', 'panels.edit.advanced_header') ?></div>
                    <div class="form-group">
                        <div class="d-flex flex-wrap mb-1">
                            <?php foreach($model->getAdvanced() as $advanced) : ?>
                                <?= $form->field($model, $advanced, [
                                    'checkboxTemplate' => $checkboxTemplateGroup, 'options' => ['tag' => false]])
                                    ->checkbox(['class' => 'custom-control-input','id' => 'form-' . $advanced])
                                    ->label(null, ['class' => 'custom-control-label'])
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group private-apikey">
                    <label for="form-apikey"><?= Yii::t('app/superadmin', 'panels.edit.apikey') ?></label>
                    <div class="input-group mb-3">
                        <?= Html::input('text', 'EditProjectForm[apikey]', $model->apikey, [
                            'id' => 'editprojectform-apikey',
                            'class' => 'form-control',
                            'data-action' => Url::toRoute(['/panels/generate-apikey'])
                        ])?>
                        <div class="input-group-append">
                            <button class="btn btn-secondary copy" id="generate-api-key" data-clipboard-target="#editprojectform-apikey" type="button"><?= Yii::t('app/superadmin', 'panels.edit.generate') ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'panels.edit.close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'panels.edit.save'), [
                    'class' => 'btn  btn-primary',
                    'name' => 'edit-providers-button',
                    'id' => 'editprojectform-save',
                    'data-dismiss' => 'modal'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
