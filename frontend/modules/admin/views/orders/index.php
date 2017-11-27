<?php

use yii\helpers\Html;
use frontend\modules\admin\components\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\UiHelper;
use frontend\modules\admin\widgets\CustomLinkPager;
use frontend\assets\OrdersAsset;
use common\models\store\Suborders;

/* @var $this yii\web\View */
/* @var $ordersDataProvider yii\data\ActiveDataProvider */
/* @var $ordersSearchModel frontend\modules\admin\models\search\OrdersSearch */

OrdersAsset::register($this);

$statusFilterButtons = $ordersSearchModel->getStatusFilterButtons([
    Suborders::STATUS_AWAITING => [
        'show_count' => true,
        'badge-class' => 'm-badge--metal'
    ],
    Suborders::STATUS_FAILED => [
        'show_count' => true,
        'badge-class' => 'm-badge--danger'
    ],
    Suborders::STATUS_ERROR => [
        'show_count' => true,
        'badge-class' => 'm-badge--danger'
    ],
]);

?>

<div class="row">

    <div class="col">

        <div class="row sommerce-block">

            <div class="col-lg-10 col-sm-12">
                <nav class="nav nav-tabs sommerce-tabs__nav">
                    <?php foreach ($statusFilterButtons as $button): ?>
                        <a class="<?= $button['active'] ? 'active' : '' ?> nav-item nav-link" href="<?= $button['url'] ?>" id="<?= $button['id'] ?>">
                            <?= $button['title'] ?>
                            <?php if(ArrayHelper::getValue($button, 'options.show_count') && $button['count'] > 0): ?>
                                <span class="<?= $button['options']['badge-class'] ?> m-badge m-badge--wide">
                                    <?= $button['count'] ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="col-lg-2 col-sm-12">
                <form action="<?= Url::toRoute('/orders') ?>">
                    <div class="input-group m-input-group--air">
                        <input type="text" class="form-control" name="query" value="<?= Html::encode(yii::$app->getRequest()->get('query')) ?>" placeholder="<?= Yii::t('admin', 'orders.search_placeholder') ?>" aria-label="<?= Yii::t('admin', 'orders.search_placeholder') ?>">
                        <?php foreach (yii::$app->getRequest()->get() as $param => $value): ?>
                            <?php if ($param !== 'query'): ?>
                                <input type="hidden" name="<?= Html::encode($param); ?>" value="<?= Html::encode($value); ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit">
                                <span class="fa fa-search"></span>
                            </button>
                        </span>
                    </div>
                </form>
            </div>

        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-orders" role="tabpanel" aria-labelledby="all-orders-tab">

                <div class="m_datatable m-datatable m-datatable--default">

                    <table class="table table-sommerce m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th class="max-width-100">Customer</th>
                            <th>Amount</th>
                            <th>Link</th>
                            <th class="sommerce-th__action">
                                <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-center"
                                     data-dropdown-toggle="click" aria-expanded="true">
                                    <a href="#" class="m-dropdown__toggle">
                                        Product
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <?php foreach ($ordersSearchModel->productFilterItems() as $item): ?>
                                                            <li class="<?= $item['active'] ? 'active' : '' ?>">
                                                                <a href="<?= $item['url'] ?>">
                                                                    <?= $item['name'] ?> (<?= $item['count'] ?>)
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="sommerce-th__action">
                                <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-center" data-dropdown-toggle="click" aria-expanded="true">
                                    <a href="#" class="m-dropdown__toggle">
                                        Mode
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <?php foreach ($ordersSearchModel->modeFilterItems() as $modeItem): ?>
                                                            <li class="<?= $modeItem['active'] ? 'active' : '' ?>">
                                                                <a href="<?= $modeItem['url'] ?>">
                                                                    <?= $modeItem['title'] ?> (<?= $modeItem['count'] ?>)
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class="sommerce-th__action-buttons"></th>
                        </tr>
                        </thead>
                        <tbody class="m-datatable__body">
                        <?php foreach ($ordersSearchModel->getOrders() as $order): ?>
                            <?=
                                $this->render('order_item', [
                                    'order' => $order,
                                    'ordersSearchModel' => $ordersSearchModel,
                                ]);
                            ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="m-datatable__pager m-datatable--paging-loaded clearfix mb-3">

                        <?= CustomLinkPager::widget(['pagination' => $ordersDataProvider->getPagination()]) ?>

                        <div class="m-datatable__pager-info">
                            <span class="m-datatable__pager-detail"><?=  UiHelper::listSummary($ordersDataProvider) ?></span>
                        </div>

                    </div>

                </div>

            </div>
        </div>
        
    </div>

</div>

<div id="suborder-details-modal" class="modal fade order-detail" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="order-detail-provider">
                        <?= Yii::t('admin', 'orders.details_provider') ?>
                    </label>
                    <input type="text" class="form-control readonly" id="order-detail-provider" value=""
                           readonly>
                </div>
                <div class="form-group">
                    <label for="order-detail-provider-id">
                        <?= Yii::t('admin', 'orders.details_order_id') ?>
                    </label>
                    <input type="text" class="form-control readonly" id="order-detail-provider-order-id" value="" readonly>
                </div>
                <div class="form-group">
                    <label for="order-detail-provider-response">
                        <?= Yii::t('admin', 'orders.details_response') ?>
                    </label>
                    <pre class="sommerce-pre readonly" id="order-detail-provider-response"></pre>
                </div>
                <div class="form-group">
                    <label for="order-detail-lastupdate">
                        <?= Yii::t('admin', 'orders.details_last_update') ?>
                    </label>
                    <input type="text" class="form-control readonly" id="order-detail-lastupdate" value="" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
