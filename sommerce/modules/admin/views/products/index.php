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

<div class="page-container">
    <div class="m-container-sommerce container-fluid">

        <div class="row sommerce-products__actions">
            <div class="col-lg-10 col-sm-12">
                <div class="page-content">
                    <button class="btn btn-primary m-btn--air" data-toggle="modal" data-target=".add_product"
                            data-backdrop="static" data-action-url="<?= Url::to(['products/create-product']) ?>">
                        <?= Yii::t('admin', 'products.create_product') ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="sommerce_dragtable">
                    <div class="sortable">
                        <?php foreach ($products as $product): ?>
                            <?= $this->render('_product_item', ['product' => $product, 'store' => $store]); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if (!$products): ?>
                    <tr>
                        <td colspan="10">
                            <div class="alert alert-warning text-center" role="alert">
                                <strong>
                                    <?= Yii::t('admin', 'products.no_products_message') ?>
                                </strong>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

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
