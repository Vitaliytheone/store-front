<?php
/* @var $this yii\web\View */
/* @var $model superadmin\models\forms\EditProviderForm */
/* @var $form control_panel\components\ActiveForm */

use control_panel\components\ActiveForm;
use superadmin\models\forms\EditProviderForm;
use yii\bootstrap\Html;
use common\models\panels\AdditionalServices;

$model = new EditProviderForm();
?>

<div class="modal fade" id="editProviderModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'providers.modal_edit_provider') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app/superadmin', 'providers.modal.close_btn') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editProviderForm',
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                    'labelOptions' => ['class' => 'form'],
                ],
            ]); ?>

            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'editProviderError'
                ]); ?>
                <div class="form-group">
                    <?= $form->field($model, 'provider_id'); ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'name'); ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'apihelp'); ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'status')
                        ->dropDownList(
                            AdditionalServices::getStatuses(),
                            ['class' => 'form-control', 'id' => 'edit-provider-status']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'type')
                        ->dropDownList(
                            AdditionalServices::getTypes(),
                            ['class' => 'form-control', 'id' => 'edit-provider-type']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'name_script'); ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'start_count')
                        ->dropDownList(
                            AdditionalServices::getStartCounts(),
                            ['class' => 'form-control', 'id' => 'edit-provider-start_count']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'refill')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'edit-provider-refill']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'cancel')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'edit-provider-cancel']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'sender_params')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'edit-provider-sender_params',
                        ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'send_method')
                        ->dropDownList(
                            AdditionalServices::getSendMethods(),
                            ['class' => 'form-control', 'id' => 'edit-provider-send_method']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_view')
                        ->dropDownList(
                            AdditionalServices::getServiceView(),
                            ['class' => 'form-control', 'id' => 'edit-provider-service_view']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_options')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'edit-provider-service_options',
                        ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'provider_service_id_label')
                        ->dropDownList(
                            Yii::$app->params['provider_service_id_label_list'],
                            ['class' => 'form-control', 'id' => 'edit-provider-provider_service_id_label']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'provider_service_settings')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'edit-provider-provider_service_settings',
                        ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'provider_service_api_error')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'edit-provider-provider_service_api_error',
                        ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_description')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'edit-provider-service_description']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_auto_min')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'edit-provider-service_auto_min']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_auto_max')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'edit-provider-service_auto_max']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'provider_rate')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'edit-provider-provider_rate']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_auto_rate')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'edit-provider-service_auto_rate']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'import')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'edit-provider-import']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'getstatus_params')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'edit-provider-getstatus_params',
                        ]) ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'providers.modal.btn_cancel') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'providers.modal.btn_save'), [
                    'class' => 'btn btn-primary',
                    'id' => 'editProviderButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
