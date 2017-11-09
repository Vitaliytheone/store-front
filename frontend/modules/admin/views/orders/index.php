<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\Ui;
use frontend\modules\admin\widgets\CustomLinkPager;

/* @var $this yii\web\View */
/* @var $ordersDataProvider frontend\modules\admin\data\OrdersActiveDataProvider */
/* @var $ordersSearchModel frontend\modules\admin\models\search\OrdersSearch */
/* @var $pagination yii\data\Pagination */


$this->title = 'Orders';

$formater = Yii::$app->formatter;
$orders = $ordersDataProvider->getOrdersWithSuborders();
$pagination = $ordersDataProvider->getPagination();

$statusFilterButtons = $ordersSearchModel->getStatusFilterButtons();
$productFilterStat = $ordersSearchModel->productFilterStat();
$modeFilterStat = $ordersSearchModel->modeFilterStat();

$summary = Ui::listSummary($ordersDataProvider);

?>

<div class="row">

    <div class="col">

        <div class="row sommerce-block">
            <div class="col-lg-10 col-sm-12">
                <nav class="nav nav-tabs sommerce-tabs__nav" role="tablist">
                    <?php foreach ($statusFilterButtons as $button): ?>
                        <a class="<?= Ui::isFilterActive('status', $button['filter']); ?> nav-item nav-link"
                           id="<?= $button['id'] ?>"
                           href="<?= $button['url'] ?>">
                            <?= $button['caption'] ?>
                            <?php if($button['stat'] && $button['stat']['count'] > 0): ?>
                                <span class="<?= $button['stat']['stat-class'] ?>">
                                    <?= ArrayHelper::getValue($button, ['stat', 'count'], null) ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
            <div class="col-lg-2 col-sm-12">
                <form class="" action="<?= Url::to('/admin/orders') ?>">
                    <div class="input-group m-input-group--air">
                        <input type="text" class="form-control" placeholder="Search for..." aria-label="Search for..."
                               name="query"
                               value="<?= Html::encode(yii::$app->getRequest()->get('query')) ?>"
                        >
                        <?php foreach (yii::$app->getRequest()->get() as $param => $value): ?>
                            <?php if ($param !== 'query'): ?>
                                <input type="hidden" name="<?= Html::encode($param); ?>" value="<?= Html::encode($value); ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button"><span class="fa fa-search"></span></button>
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
                                                    <!-- Product filter -->
                                                    <?php foreach ($productFilterStat as $productItem): ?>
                                                        <li class="<?= Ui::isFilterActive('product', $productItem['product']); ?>">
                                                            <a href="<?= $productItem['product'] === -1 ? Url::current(['product' => null]) : Url::current(['product' => $productItem['product']]); ?>">
                                                                <?= $productItem['name'].' ('.$productItem['cnt'].')' ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                    <!--/ Product filter -->
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
                                <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-center"
                                     data-dropdown-toggle="click" aria-expanded="true">
                                    <a href="#" class="m-dropdown__toggle">
                                        Mode
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <!-- Mode filter -->
                                                        <?php foreach ($modeFilterStat as $modeItem): ?>
                                                            <li class="<?= Ui::isFilterActive('mode', $modeItem['mode']); ?>">
                                                                <a href="<?= $modeItem['mode'] === -1 ? Url::current(['mode' => null]) : Url::current(['mode' => $modeItem['mode']]); ?>">
                                                                    <?= $modeItem['name'].' ('.$modeItem['cnt'].')' ?>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        <!--/ Mode filter -->
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
                        <?php foreach ($orders as $orderId => $order): ?>
                            <!-- Order item -->
                            <?= $this->render('order_item', ['order' => $order, 'ordersSearchModel' => $ordersSearchModel]); ?>
                            <!--/ Order item -->
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="m-datatable__pager m-datatable--paging-loaded clearfix mb-3">
                        <?=
                        CustomLinkPager::widget([
                            'pagination' => $pagination,
                            'maxButtonCount' => 10,
                            'disableCurrentPageButton' => false,
                            'hideOnSinglePage' => true,

                            'activePageCssClass' => 'm-datatable__pager-link-number m-datatable__pager-link--active',
                            'disabledPageCssClass' => 'm-datatable__pager-link--disabled',

                            'firstPageCssClass' => 'm-datatable__pager-link--first',
                            'firstPageLabel' => '<i class="la la-angle-double-left"></i>',

                            'lastPageCssClass' => 'm-datatable__pager-link--last',
                            'lastPageLabel' => '<i class="la la-angle-double-right"></i>',

                            'prevPageCssClass' => 'm-datatable__pager-link--prev',
                            'prevPageLabel' => '<i class="la la-angle-left"></i>',

                            'nextPageCssClass' => 'm-datatable__pager-link--next',
                            'nextPageLabel' => '<i class="la la-angle-right"></i>',

                            'pageCssClass' => 'm-datatable__pager-link-number',

                            'options' => ['class' => 'm-datatable__pager-nav'],
                            'linkOptions' => ['class' => 'm-datatable__pager-link'],

                        ])
                        ?>

                        <div class="m-datatable__pager-info">
                            <span class="m-datatable__pager-detail"><?= $summary ?></span>
                        </div>

                    </div>
                    <!--/ Pagination -->

                </div>

            </div>
        </div>
        
    </div>

</div>

<!-- Order Details modal -->
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
                    <label for="order-detail-provider">Provider</label>
                    <input type="text" class="form-control readonly" id="order-detail-provider" value=""
                           readonly>
                </div>
                <div class="form-group">
                    <label for="order-detail-provider-id">Provider's order ID</label>
                    <input type="text" class="form-control readonly" id="order-detail-provider-order-id" value="" readonly>
                </div>
                <div class="form-group">
                    <label for="order-detail-provider-response">Provider's response</label>
                    <pre class="sommerce-pre readonly" id="order-detail-provider-response"></pre>
                </div>
                <div class="form-group">
                    <label for="order-detail-lastupdate">Last update</label>
                    <input type="text" class="form-control readonly" id="order-detail-lastupdate" value="" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Order Details modal -->
