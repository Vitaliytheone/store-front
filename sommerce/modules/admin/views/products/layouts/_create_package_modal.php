<?php

use admin\models\forms\package\CreatePackageForm;
use common\components\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $store \common\models\sommerces\Stores */

$model = new CreatePackageForm();
$model->setStore($store);
?>

<!--Add package-->
<div class="modal fade add_package" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="createPackageModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'products.create_package.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'createPackageForm',
                'action' => Url::toRoute('/products/create-package'),
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
                    'id' => 'createPackageError'
                ]); ?>

                <?= $form->field($model, 'name') ?>

                <?= $form->field($model, 'price')->textInput([
                    'type' => 'number',
                    'min' => '0.01',
                    'max' => MAX_MYSQL_INT,
                    'step' => '0.01',
                ]) ?>

                <?= $form->field($model, 'quantity') ?>

                <?= $form->field($model, 'link_type')->dropDownList($model->getLinkTypes()) ?>

                <?= $form->field($model, 'visibility')->dropDownList($model->getVisibilityVariants()) ?>

                <?= $form->field($model, 'mode')->dropDownList($model->getModeVariants()) ?>

                <div id="create-package-auto" class="hidden">
                    <hr>
                    <?= $form->field($model, 'provider_id')->dropDownList($model->getStoreProviders(), [
                        'prompt' => Yii::t('admin', 'products.package_provider_default'),
                        'class' => 'form-control provider-id',
                    ]) ?>

                    <?= $form->field($model, 'provider_service')->dropDownList($model->getProviderServices(), [
                        'class' => 'form-control provider-service',
                    ]) ?>

                    <span class="api-error m--font-danger hidden"></span>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <!--Empty-->
                </div>
                <div>
                    <?= Html::a(Yii::t('admin', 'products.create_package.cancel_btn'), '#', [
                        'class' => 'btn btn-secondary mr-3',
                        'data-dismiss' => 'modal',
                    ]) ?>
                    <?= Html::submitButton(Yii::t('admin', 'products.create_package.submit_btn'), [
                        'class' => 'btn btn-primary',
                        'id' => 'createPackageButton',
                    ])?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>