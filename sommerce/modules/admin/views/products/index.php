<?php

use yii\helpers\Url;
use sommerce\assets\ProductsAsset;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $store \common\models\stores\Stores */
/* @var $storeProviders array */
/** @var $products array  Products with packages array */

ProductsAsset::register($this);
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
