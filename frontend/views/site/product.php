<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\Ui;

/* @var $product frontend\models\forms\ProductViewForm */

$this->title = $product->name;
?>

<section class="min-height">
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-title"><?= Html::encode($product->name) ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="content">
                    <?= $product->description ?>
                </div>
            </div>
        </div>
        <div class="row products">
            <!--  Package items list -->
            <?php foreach ($product->packages as $package): ?>
                <!--  Package item  -->
                <div class="col-lg-3">
                    <div class="product-card text-center <?=  Ui::toggleString($package->best, 'best-product') ?>">
                        <div class="product-quantity"><?= $package->quantity ?></div>
                        <h3 class="product-title"><?= Html::encode($package->name) ?></h3>
                        <div class="product-price">$<?= $package->price ?></div>
                        <div class="product-properties">
                            <ul>
                                <?php foreach ($product->properties as $property): ?>
                                    <li><?= Html::encode($property) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="product-actions">
                            <a href="<?= Url::to(['/add-to-cart', 'id' => $package->id]) ?>" class="btn btn-block btn-default">Buy now</a>
                        </div>
                    </div>
                </div>
                <!--/ Package item  -->
            <?php endforeach; ?>
            <!--/ Package items list -->
        </div>
    </div>
</section>