<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $store \common\models\stores\Stores */
/** @var $products array Products with packages array */

?>

<?php if (!empty($products)) : ?>
    <div class="sortable">
        <?php foreach ($products as $product) : ?>
            <div class="sommerce-products-editor__product">
                <div class="sommerce-products-editor__product-title">
                    <div class="sommerce-products-editor__product-icon move" data-action-url="<?= Url::to(['products/move-product', 'id' => $product['id'], 'position' => ""]) ?>"></div>
                    <div class="sommerce-products-editor__product-name <?= (!$product['visibility'] ? 'disabled-product-item' : '') ?>">
                        <?= Html::encode($product['name']) ?>
                        <?= Html::a(Yii::t('admin', 'products.edit_product'), Url::to(['products/update-product', 'id' => $product['id']]), [
                            'class' => 'sommerce-products-editor__product-edit',
                            'data' => [
                                //'details' => $product
                            ],
                        ])?>
                    </div>
                </div>
                <div class="sommerce-products-editor__packages">
                    <?= $this->render('_packages_list', [
                        'packages' => (array)ArrayHelper::getValue($product, 'packages', [])
                    ])?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
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
