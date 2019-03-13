<?php

use admin\models\forms\package\EditPackageForm;
use common\components\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $store \common\models\sommerces\Stores */

$model = new EditPackageForm();
$model->setStore($store);
?>
<!--Add package-->
<div class="modal fade edit-package" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="editPackageModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'products.edit_package.header') ?> (ID: <span id="packageId"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'editPackageForm',
                'action' => Url::toRoute('/products/edit-package'),
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

                <?= Html::activeHiddenInput($model, 'id') ?>

                <div id="edit-package-auto" class="hidden">
                    <hr>
                    <?= $form->field($model, 'provider_id')->dropDownList($model->getStoreProviders(), [
                        'prompt' => ['text' => Yii::t('admin', 'products.package_provider_default'),
                            'options' => ['disabled' => true, 'selected' => true,]],
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
                    <div class="btn btn-modal-delete">
                        <div class="sommerce-dropdown__delete">
                            <div class="sommerce-dropdown__delete-description">
                                <?= Yii::t('admin', 'products.edit_package.delete_description') ?>
                            </div>
                            <?= Html::a(Yii::t('admin', 'products.edit_package.cancel_link'), '#', [
                                'class' => 'btn btn-danger btn-sm mr-2 sommerce-dropdown__delete-cancel',
                            ])?>
                            <?= Html::a(Yii::t('admin', 'products.edit_package.delete_link'), Url::toRoute(['products/delete-package']), [
                                'class' => 'btn btn-secondary btn-sm delete-package',
                            ])?>
                        </div>
                        <?= Yii::t('admin', 'products.edit_package.delete_link') ?>
                    </div>
                </div>
                <div>
                    <?= Html::a(Yii::t('admin', 'products.edit_package.cancel_btn'), '#', [
                        'class' => 'btn btn-secondary mr-3',
                        'data-dismiss' => 'modal',
                    ]) ?>
                    <?= Html::submitButton(Yii::t('admin', 'products.edit_package.submit_btn'), [
                        'class' => 'btn btn-primary',
                        'id' => 'editPackageButton',
                    ])?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
