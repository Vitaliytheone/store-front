<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use common\models\stores\Stores;

/* @var $this yii\web\View */
/* @var $store \common\models\stores\Stores */
/** @var $products array Products with packages array */

?>

<?php if (!empty($products)) : ?>
    <div class="sortable">
        <div class="sommerce-products-editor__product">
            <div class="sommerce-products-editor__product-title">
                <div class="sommerce-products-editor__product-icon move"></div>
                <div class="sommerce-products-editor__product-name">Buy instagram likes
                    <a href="#" class="sommerce-products-editor__product-edit" data-toggle="modal" data-target=".edit-product" data-backdrop="static">Edit</a>
                </div>
            </div>
            <div class="sommerce-products-editor__packages">
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
                    <tr>
                        <td>
                            <span class="sommerce-products-editor__packages-drag"></span>
                            <span class="sommerce-products-editor__packages-quantity">1000</span>
                            <span class="sommerce-products-editor__packages-name">Likes</span>
                        </td>
                        <td>
                            justanotherpanel.com
                        </td>
                        <td>
                            $19.10
                        </td>
                        <td class="sommerce-products-editor__table-td-actions">
                            <div class="sommerce-products-editor__packages-actions">
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".duplicate" data-backdrop="static"><span class="la la-clone"></span> Duplicate</a>
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".edit-package" data-backdrop="static"><span class="la la-edit"></span> Edit</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="sommerce-products-editor__packages-drag"></span>
                            <span class="sommerce-products-editor__packages-quantity">1000</span>
                            <span class="sommerce-products-editor__packages-name">Likes</span>
                        </td>
                        <td>
                            justanotherpanel.com
                        </td>
                        <td>
                            $19.10
                        </td>
                        <td class="sommerce-products-editor__table-td-actions">
                            <div class="sommerce-products-editor__packages-actions">
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".duplicate" data-backdrop="static"><span class="la la-clone"></span> Duplicate</a>
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".edit-package" data-backdrop="static"><span class="la la-edit"></span> Edit</a>
                            </div>
                        </td>
                    </tr>
                    <tr class="sommerce-products-editor__packages-disabled">
                        <td>
                            <span class="sommerce-products-editor__packages-drag"></span>
                            <span class="sommerce-products-editor__packages-quantity">1000</span>
                            <span class="sommerce-products-editor__packages-name">Likes</span>
                        </td>
                        <td>
                            justanotherpanel.com
                        </td>
                        <td>
                            $19.10
                        </td>
                        <td class="sommerce-products-editor__table-td-actions">
                            <div class="sommerce-products-editor__packages-actions">
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".duplicate" data-backdrop="static"><span class="la la-clone"></span> Duplicate</a>
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".edit-package" data-backdrop="static"><span class="la la-edit"></span> Edit</a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="sommerce-products-editor__packages-add-page">
                    <a href="#" class="sommerce-products-editor__packages-add-link" data-toggle="modal" data-target=".add_package" data-backdrop="static">+ Add package</a>
                </div>
            </div>
        </div>
        <div class="sommerce-products-editor__product">
            <div class="sommerce-products-editor__product-title">
                <div class="sommerce-products-editor__product-icon move"></div>
                <div class="sommerce-products-editor__product-name">Buy instagram likes
                    <a href="#" class="sommerce-products-editor__product-edit" data-toggle="modal" data-target=".edit-product" data-backdrop="static">Edit</a>
                </div>
            </div>
            <div class="sommerce-products-editor__packages">
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
                    <tr>
                        <td>
                            <span class="sommerce-products-editor__packages-drag"></span>
                            <span class="sommerce-products-editor__packages-quantity">1000</span>
                            <span class="sommerce-products-editor__packages-name">Likes</span>
                        </td>
                        <td>
                            justanotherpanel.com
                        </td>
                        <td>
                            $19.10
                        </td>
                        <td class="sommerce-products-editor__table-td-actions">
                            <div class="sommerce-products-editor__packages-actions">
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".duplicate" data-backdrop="static"><span class="la la-clone"></span> Duplicate</a>
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".edit-package" data-backdrop="static"><span class="la la-edit"></span> Edit</a>
                            </div>
                        </td>
                    </tr>
                    <tr class="sommerce-products-editor__packages-disabled">
                        <td>
                            <span class="sommerce-products-editor__packages-drag"></span>
                            <span class="sommerce-products-editor__packages-quantity">1000</span>
                            <span class="sommerce-products-editor__packages-name">Likes</span>
                        </td>
                        <td>
                            justanotherpanel.com
                        </td>
                        <td>
                            $19.10
                        </td>
                        <td class="sommerce-products-editor__table-td-actions">
                            <div class="sommerce-products-editor__packages-actions">
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".duplicate" data-backdrop="static"><span class="la la-clone"></span> Duplicate</a>
                                <a href="#" class="sommerce-products-editor__packages-actions-link" data-toggle="modal" data-target=".edit-package" data-backdrop="static"><span class="la la-edit"></span> Edit</a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="sommerce-products-editor__packages-add-page">
                    <a href="#" class="sommerce-products-editor__packages-add-link" data-toggle="modal" data-target=".add_package" data-backdrop="static">+ Add package</a>
                </div>
            </div>
        </div>
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
