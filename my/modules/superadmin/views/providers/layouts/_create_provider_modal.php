<?php
/* @var $this yii\web\View */
/* @var $model my\modules\superadmin\models\forms\EditProviderForm */
/* @var $form my\components\ActiveForm */

use my\components\ActiveForm;
use my\modules\superadmin\models\forms\CreateProviderForm;
use yii\bootstrap\Html;
use common\models\panels\AdditionalServices;

$model = new CreateProviderForm();
?>

<div class="modal fade" id="createProviderModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'providers.modal_create.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app/superadmin', 'providers.modal.close_btn') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'createProviderForm',
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                    'labelOptions' => ['class' => 'form'],
                ],
            ]); ?>

            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'createProviderError'
                ]); ?>
                <div class="form-group">
                    <?= $form->field($model, 'name')->textInput(['id' => 'create-provider-name']); ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'apihelp')->textInput(['id' => 'create-provider-apihelp']); ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'status')
                        ->dropDownList(
                            AdditionalServices::getStatuses(),
                            ['class' => 'form-control', 'id' => 'create-provider-status']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'type')
                        ->dropDownList(
                            AdditionalServices::getTypes(),
                            ['class' => 'form-control', 'id' => 'create-provider-type']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'name_script')->textInput(['id' => 'create-provider-name_script']); ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'start_count')
                        ->dropDownList(
                            AdditionalServices::getStartCounts(),
                            ['class' => 'form-control', 'id' => 'create-provider-start_count']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'refill')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'create-provider-refill']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'cancel')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'create-provider-cancel']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'sender_params')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'create-provider-sender_params',
                        ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'send_method')
                        ->dropDownList(
                            AdditionalServices::getSendMethods(),
                            ['class' => 'form-control', 'id' => 'create-provider-send_method']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_view')
                        ->dropDownList(
                            AdditionalServices::getServiceView(),
                            ['class' => 'form-control', 'id' => 'create-provider-service_view']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_options')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'create-provider-service_options',
                        ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'provider_service_id_label')
                        ->dropDownList(
                            isset(Yii::$app->params['provider_service_id_label_list']) ? Yii::$app->params['provider_service_id_label_list'] : [],
                            ['class' => 'form-control', 'id' => 'create-provider-provider_service_id_label']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'provider_service_settings')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'create-provider-provider_service_settings',
                        ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'provider_service_api_error')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'create-provider-provider_service_api_error',
                        ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_description')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'create-provider-service_description']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_auto_min')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'create-provider-service_auto_min']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_auto_max')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'create-provider-service_auto_max']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'provider_rate')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'create-provider-provider_rate']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'service_auto_rate')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'create-provider-service_auto_rate']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'import')
                        ->dropDownList(
                            AdditionalServices::getDefaultBool(),
                            ['class' => 'form-control', 'id' => 'create-provider-import']
                        ) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'getstatus_params')
                        ->textarea([
                            'style' => 'height:400px;',
                            'class' => 'form-control',
                            'id' => 'create-provider-getstatus_params',
                        ]) ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'providers.modal.btn_cancel') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'providers.modal_create.create_btn'), [
                    'class' => 'btn btn-primary',
                    'id' => 'createProviderButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
