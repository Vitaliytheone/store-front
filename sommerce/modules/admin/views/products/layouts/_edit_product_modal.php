<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use admin\models\forms\product\EditProductForm;
use common\components\ActiveForm;

/* @var $this yii\web\View */
/* @var $store \common\models\sommerces\Stores */

$model = new EditProductForm();
$model->setStore($store);
?>
<!--Edit product-->
<div class="modal fade edit-product" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="editProductModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'products.edit_product.header') ?> (ID: <span id="productId"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editProductForm',
                'action' => Url::toRoute('/products/update-product'),
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
            </div>


            <div class="modal-footer justify-content-end">
                <?= Html::a(Yii::t('admin', 'products.edit_product.cancel_btn'), '#', [
                    'class' => 'btn btn-secondary mr-3',
                    'data-dismiss' => 'modal',
                ]) ?>
                <?= Html::submitButton(Yii::t('admin', 'products.edit_product.submit_btn'), [
                    'class' => 'btn btn-primary',
                    'id' => 'editProductButton',
                ])?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>