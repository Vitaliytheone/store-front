<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $store \common\models\sommerces\Stores */
/** @var $products array Products with packages array */

?>

<?php if (!empty($products)) : ?>
    <div class="sortable sommerce_dragtable">
        <?php foreach ($products as $product) : ?>
            <div class="sommerce-products-editor__product product-item"  data-action-url="<?= Url::to(['products/move-product', 'id' => $product['id'], 'position' => ""]) ?>">
                <div class="sommerce-products-editor__product-title">
                    <div class="sommerce-products-editor__product-icon move"></div>
                    <div class="sommerce-products-editor__product-name <?= (!$product['visibility'] ? 'disabled-product-item' : '') ?>">
                        <?= Html::encode($product['name']) ?>
                        <?= Html::a(Yii::t('admin', 'products.edit_product'), Url::to(['products/update-product', 'id' => $product['id']]), [
                            'class' => 'sommerce-products-editor__product-edit edit-product',
                            'data' => [
                                'details' => [
                                    'id' => $product['id'],
                                    'name' => $product['name'],
                                ]
                            ],
                        ])?>
                    </div>
                </div>
                <div class="sommerce-products-editor__packages">
                    <?= $this->render('_packages_list', [
                        'packages' => (array)ArrayHelper::getValue($product, 'packages', [])
                    ])?>

                    <div class="sommerce-products-editor__packages-add-page">
                        <?= Html::a('+ ' . Yii::t('admin', 'products.add_package'), Url::to(['products/create-package', 'id' => $product['id']]), [
                            'class' => 'sommerce-products-editor__packages-add-link create-package',
                        ])?>
                    </div>
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
