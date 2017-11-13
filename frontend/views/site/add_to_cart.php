<?php
    /* @var $this yii\web\View */
    /* @var $form \common\components\ActiveForm */
    /* @var $model \frontend\models\forms\AddToCartForm */
    /* @var $package \common\models\store\Packages */
    /* @var $goBack string */

    use common\components\ActiveForm;
    use yii\bootstrap\Html;
?>

<section class="min-height">
    <div class="container">
        <form id="addToCardForm" class="form" action="" method="post">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-title">Add to Cart</h1>
                </div>
            </div>

            <div class="row order">
                <div class="col-lg-6">
                    <div class="bg-white">
                        <div class="order-block">
                            <table class="table table-bordered">
                                <tr>
                                    <th class="col-lg-3">Package name</th>
                                    <td><?= $package->name ?></td>
                                </tr>
                                <tr>
                                    <th class="col-lg-3">Quantity</th>
                                    <td><?= $package->quantity ?></td>
                                </tr>
                                <tr>
                                    <th class="col-lg-3">Price</th>
                                    <td><?= $package->price ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-white">
                        <div class="order-block">
                            <?php if (!empty($error)) : ?>
                                <div class="error-summary alert alert-danger"><?= $errorText ?></div>
                            <?php endif; ?>

                            <div class="form-group field-addtocartform-link required">
                                <label class="control-label" for="addtocartform-link">Link</label>
                                <input type="text" id="addtocartform-link" class="form-control" name="AddToCartForm[link]" aria-required="true" value="<?= $data['link'] ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <a class="btn btn-default" href="<?= $goBackUrl ?>">Cancel</a>
                </div>
                <div class="col-lg-6 text-right">
                    <button type="submit" id="addToCartButton" class="btn btn-primary" name="add-to-cart-button">Add to cart</button>
                </div>
            </div>

            <input type="hidden" name="<?= $csrfname ?>" value="<?= $csrftoken ?>">
        </form>
    </div>
</section>