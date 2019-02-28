<?php

use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $store \common\models\sommerces\Stores */
/* @var $storeProviders array */
/** @var $products array  Products with packages array */
?>

<!-- Page Content Start -->
<div class="page-container">
    <div class="m-container-sommerce container-fluid">
        <div class="row sommerce-products__actions">

            <div class="col-lg-12">
                <div class="page-content">
                    <?= Html::a(Yii::t('admin', 'products.create_product'), Url::toRoute(['products/create-product']), [
                        'class' => 'btn btn-primary m-btn--air',
                        'id' => 'createProduct',
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-12">
                <div class="sommerce-products-editor">
                    <?= $this->render('layouts/_products_list', [
                        'products' => $products
                    ])?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('layouts/_create_product_modal', [
    'store' => $store,
]) ?>

<?= $this->render('layouts/_edit_product_modal', [
    'store' => $store,
]) ?>

<?= $this->render('layouts/_create_package_modal', [
    'store' => $store,
]) ?>

<?= $this->render('layouts/_edit_package_modal', [
    'store' => $store,
]) ?>
