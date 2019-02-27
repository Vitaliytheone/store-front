<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/** @var $packages array Packages */

?>
<table class="sommerce-products-editor__packages-table">
    <thead>
    <tr>
        <th class="sommerce-products-editor__table-th-name">Name</th>
        <th>Provider</th>
        <th>Price</th>
        <th class="sommerce-products-editor__table-th-actions"></th>
    </tr>
    </thead>
    <tbody class="sortable-packages">
        <?php foreach ($packages as $package) : ?>
            <tr class="<?= ($package['visibility'] ? 'disabled-product' : '') ?>">
                <td>
                    <span class="sommerce-products-editor__packages-drag"></span>
                    <span class="sommerce-products-editor__packages-quantity"><?= $package['quantity']?></span>
                    <span class="sommerce-products-editor__packages-name"><?= Html::encode($package['name']) ?></span>
                </td>
                <td>
                    <?= (!empty($package['mode']) ? $package['provider'] : 'Manual') ?>
                </td>
                <td>
                    <?= $package['price'] ?>
                </td>
                <td class="sommerce-products-editor__table-td-actions">
                    <div class="sommerce-products-editor__packages-actions">
                        <?= Html::a('<span class="la la-clone"></span> ' . Yii::t('admin', 'products.duplicate_package'), Url::to(['products/duplicate-package']), [
                            'class' => 'sommerce-products-editor__packages-actions-link',
                        ])?>
                        <?= Html::a('<span class="la la-edit"></span> ' . Yii::t('admin', 'products.edit_package'), Url::to(['products/edit-package']), [
                            'class' => 'sommerce-products-editor__packages-actions-link',
                        ])?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="sommerce-products-editor__packages-add-page">
    <?= Html::a('+ ' . Yii::t('admin', 'products.add_package'), Url::to(['products/create-package']), [
        'class' => 'sommerce-products-editor__packages-add-link',
    ])?>
</div>