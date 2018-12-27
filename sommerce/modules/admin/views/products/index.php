<?php

use yii\helpers\Url;
use sommerce\assets\ProductsAsset;

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
                        <?= Yii::t('admin', 'products.add_product') ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="sommerce_dragtable">
                    <div class="sortable">
                        <?php foreach ($products as $product): ?>
                            <?= $this->render('_product_item', ['product' => $product]); ?>
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

<?= $this->render('_modal_product_form', ['store' => $store]); ?>
<?= $this->render('_modal_package_form', ['storeProviders' => $storeProviders,]); ?>
<?= $this->render('_modal_delete_package', []); ?>