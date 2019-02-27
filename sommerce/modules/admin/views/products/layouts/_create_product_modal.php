<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use admin\models\forms\product\CreateProductForm;
use common\components\ActiveForm;

/* @var $this yii\web\View */
/* @var $store \common\models\stores\Stores */

$model = new CreateProductForm();
$model->setStore($store);
?>
<!--Add product-->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="createProductModal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'products.create_product.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'createProductForm',
                'action' => Url::toRoute('/products/create-product'),
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
                    'id' => 'createProductError'
                ]); ?>

                <?= $form->field($model, 'name') ?>

                <?= Html::activeHiddenInput($model, 'url')?>

                <div class="form-group">
                    <div class="sommerce-create-product">
                        <div class="sommerce-create-product__title">
                            <?= $model->getAttributeLabel('create_page')?>
                        </div>
                        <div class="sommerce-create-product__url">
                            <?= $model->getStore()->getSite() ?>/<span class="page-url"></span>
                        </div>
                        <div class="sommerce-create-product__switch">

                        <span class="m-switch m-switch--primary m-switch--sm">
                            <label>
                                <?= Html::activeCheckbox($model, 'create_page')?>
                                <span></span>
                            </label>
                        </span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal-footer justify-content-end">
                <?= Html::a(Yii::t('admin', 'products.create_product.cancel_btn'), '#', [
                    'class' => 'btn btn-secondary mr-3',
                    'data-dismiss' => 'modal',
                ]) ?>
                <?= Html::submitButton(Yii::t('admin', 'products.create_product.submit_btn'), [
                    'class' => 'btn btn-primary',
                    'id' => 'createProductButton',
                ])?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
