<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\Ui;
use frontend\assets\ProductsAsset;

/* @var $this yii\web\View */
/* @var $formatter yii\i18n\Formatter */
/* @var common\models\stores\StoreProviders[] $storeProviders  */

$this->title = 'Products';
$formatter = Yii::$app->formatter;
$products = [
        [
           'id' => 103,
           'packages' => [
               ['id' => 7, 'product_id' => 103],
               ['id' => 8, 'product_id' => 103],
               ['id' => 9, 'product_id' => 103],
           ]
        ],
        [
           'id' => 104,
           'packages' => [
               ['id' => 6, 'product_id' => 104],
           ]
        ],
];

ProductsAsset::register($this);
?>

<!-- Product add/search -->
<div class="row sommerce-products__actions">

    <div class="col-lg-10 col-sm-12">
        <div class="page-content">
            <button class="btn btn-primary m-btn--air"
                    data-toggle="modal"
                    data-target=".add_product"
                    data-backdrop="static"
                    data-action-url="<?= Url::to(['products/create-product']) ?>">
                Add product
            </button>
        </div>
    </div>
</div>
<!--/ Product add/search -->

<!-- Products-Packages list -->
<div class="row">
    <div class="col-12">
        <div class="sommerce_dragtable">
            <div class="sortable">
                <?php foreach ($products as $product): ?>
                    <!-- Product item -->
                    <?= $this->render('product_item', ['product' => $product]); ?>
                    <!--/ Product item -->
                <?php endforeach; ?>
            </div>
        </div>
        <?php if(!$products): ?>
            <tr>
                <td colspan="10">
                    <div class="alert alert-warning text-center" role="alert">
                        <strong>No products were found!</strong>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
    </div>
</div>
<!-- Products-Packages list -->

<!-- Modal `Add/Edit Product` -->
<?= $this->render('modal_add_product', []); ?>
<!--/ Modal `Add/Edit Product` -->

<!-- Modal Add/Edit Package -->
<?= $this->render('modal_add_package', ['storeProviders' => $storeProviders,]); ?>
<!--/ Modal Add/Edit Package -->

<!-- Modal Delete Package -->
<?= $this->render('modal_delete_package', []); ?>
<!--/ Modal Delete Package -->