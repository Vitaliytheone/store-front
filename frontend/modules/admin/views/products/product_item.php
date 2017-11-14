<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\helpers\Ui;

/* @var $this yii\web\View */
/* @var $product array */
/* @var $package array */
/* @var $formatter yii\i18n\Formatter */

$formatter = Yii::$app->formatter;
$packages = ArrayHelper::getValue($product, 'packages', []);

?>

<div class="row group-caption product-item" data-action-url="<?= Url::to(['products/move-product', 'id' => $product['id'], 'position' => ""]) ?>">
    <div class="col-12 sommerce_dragtable__category <?= Ui::toggleString(!$product['visibility'], 'disabled-product-item') ?>">
        <div class="sommerce_dragtable__category-title">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="sommerce_dragtable__category-move move">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Drag-Handle</title>
                            <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                        </svg>
                    </div>
                    <?= Html::encode($product['name']) ?>
                    <a href="#" class="btn btn-outline-primary btn-sm m-btn m-btn--icon m-btn--pill edit-button"
                       data-toggle="modal"
                       data-target=".add_product"
                       data-id="<?= $product['id'] ?>"
                       data-get-url="<?= Url::to(['products/get-product', 'id' => $product['id']]) ?>"
                       data-action-url="<?= Url::to(['products/update-product', 'id' => $product['id']]) ?>">
                        Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 group-items">
        <?php foreach ($packages as $package): ?>
            <!-- Package Item -->
            <?= $this->render('package_item', ['package' => $package]); ?>
            <!--/ Package Item-->
        <?php endforeach; ?>
        <!-- Button Add Package -->
        <div class="mt-2 mb-3">
            <button class="btn btn-primary btn-sm m-btn m-btn--icon btm-sm"
                    data-toggle="modal"
                    data-target=".add_package"
                    data-backdrop="static"
                    data-product_id="<?= $product['id'] ?>"
                    data-action-url="<?= Url::to(['products/create-package']) ?>">
                Add package
            </button>
        </div>
        <!--/ Button Add Package -->
    </div>
</div>






