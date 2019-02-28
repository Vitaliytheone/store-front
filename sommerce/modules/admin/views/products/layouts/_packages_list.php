<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $packages array Packages */

?>
<table class="sommerce-products-editor__packages-table">
    <thead>
    <tr>
        <th class="sommerce-products-editor__table-th-name"><?= Yii::t('admin', 'products.packages.column.name') ?></th>
        <th><?= Yii::t('admin', 'products.packages.column.provider') ?></th>
        <th><?= Yii::t('admin', 'products.packages.column.price') ?></th>
        <th class="sommerce-products-editor__table-th-actions"></th>
    </tr>
    </thead>
    <tbody class="sortable-packages">
        <?php foreach ($packages as $package) : ?>
            <tr class="package-item <?= ($package['visibility'] ? 'disabled-product' : '') ?>" data-action-url="<?= Url::to(['products/move-package', 'id' => $package['id'], 'position' => ""])?>">
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
                        <?= Html::a('<span class="la la-edit"></span> ' . Yii::t('admin', 'products.edit_package'), Url::to(['products/update-package', 'id' => $package['id']]), [
                            'class' => 'sommerce-products-editor__packages-actions-link edit-package',
                            'data' => [
                                'details' => $package,
                                'delete_link' => Url::toRoute(['products/delete-package', 'id' => $package['id']]),
                            ]
                        ])?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>