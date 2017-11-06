<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\Ui;
use frontend\assets\ProductsAsset;

/* @var $this yii\web\View */
/* @var $formatter yii\i18n\Formatter */

$this->title = 'Products';
$formatter = Yii::$app->formatter;
$products = [
        '1' => [
           'id' => 1,
           'packages' => [
                   ['id' => 1, ],
                   ['id' => 2, ],
                   ['id' => 3, ],
           ]
        ],
        '2' => [
           'id' => 2,
           'packages' => [
                   ['id' => 4, ],
                   ['id' => 5, ],
           ]
        ]
];

ProductsAsset::register($this);
?>

<!-- Product Search -->
<div class="row sommerce-products__actions">

    <div class="col-lg-10 col-sm-12">
        <div class="page-content">
            <button class="btn btn-primary m-btn--air" data-toggle="modal" data-target=".add_product" data-backdrop="static">Add product</button>
            <button class="btn btn-primary m-btn--air" data-toggle="modal" data-target=".add_package" data-backdrop="static">Add package</button>
        </div>
    </div>
    <div class="col-lg-2 col-sm-12 d-flex align-items-center">
        <div class="input-group m-input-group--air">
            <input type="text" class="form-control" placeholder="Search for..." aria-label="Search for...">
            <span class="input-group-btn">
                        <button class="btn btn-primary" type="button"><span class="fa fa-search"></span></button>
                      </span>
        </div>
    </div>
</div>
<!--/ Product Search -->

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
<?= $this->render('modal_add_package', []); ?>
<!--/ Modal Add/Edit Package -->