<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use frontend\helpers\Ui;

/* @var $this yii\web\View */
/* @var $ordersDataProvider frontend\modules\admin\data\OrdersActiveDataProvider */
/* @var $orderSearchModel frontend\modules\admin\models\search\OrdersSearch */

$this->title = 'Orders';

$formater = Yii::$app->formatter;
$orders = $ordersDataProvider->getOrdersSuborders();
$pagination = $ordersDataProvider->getPagination();

$statusFilterButtons = $orderSearchModel->getStatusFilterButtons();
$productFilterStat = $orderSearchModel->productFilterStat();
$modeFilterStat = $orderSearchModel->modeFilterStat();

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
                <form class="form-inline" action="<?= Url::to('/admin/orders') ?>">
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
                            <?= $this->render('order-item', ['order' => $order]); ?>
                            <!--/ Order item -->
                        <?php endforeach; ?>
                        <?php if(!$orders): ?>
                            <tr>
                                <td colspan="10">
                                    <div class="alert alert-warning text-center" role="alert">
                                        <strong>No orders were found!</strong>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="m-datatable__pager m-datatable--paging-loaded clearfix mb-3">
                        <ul class="m-datatable__pager-nav">
                            <li><a title="Primero"
                                   class="m-datatable__pager-link m-datatable__pager-link--first m-datatable__pager-link--disabled"
                                   data-page="1" disabled="disabled"><i class="la la-angle-double-left"></i></a></li>
                            <li><a title="Anterior"
                                   class="m-datatable__pager-link m-datatable__pager-link--prev m-datatable__pager-link--disabled"
                                   data-page="1" disabled="disabled"><i class="la la-angle-left"></i></a></li>
                            <li style="display: none;"><a title="Más páginas"
                                                          class="m-datatable__pager-link m-datatable__pager-link--more-prev"
                                                          data-page="1"><i class="la la-ellipsis-h"></i></a></li>
                            <li style="display: none;"><input type="text" class="m-pager-input form-control"
                                                              title="Número de página"></li>
                            <li style=""><a
                                        class="m-datatable__pager-link m-datatable__pager-link-number m-datatable__pager-link--active"
                                        data-page="1">1</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                            data-page="2">2</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                            data-page="3">3</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                            data-page="4">4</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                            data-page="5">5</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                            data-page="6">6</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="7">7</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="8">8</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="9">9</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="10">10</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="11">11</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="12">12</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="13">13</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="14">14</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="15">15</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="16">16</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="17">17</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="18">18</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="19">19</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="20">20</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="21">21</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="22">22</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="23">23</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="24">24</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="25">25</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="26">26</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="27">27</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="28">28</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="29">29</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="30">30</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="31">31</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="32">32</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="33">33</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="34">34</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number"
                                                          data-page="35">35</a></li>
                            <li><a title="Más páginas"
                                   class="m-datatable__pager-link m-datatable__pager-link--more-next" data-page="7"><i
                                            class="la la-ellipsis-h"></i></a></li>
                            <li><a title="Siguiente" class="m-datatable__pager-link m-datatable__pager-link--next"
                                   data-page="2"><i class="la la-angle-right"></i></a></li>
                            <li><a title="Último" class="m-datatable__pager-link m-datatable__pager-link--last"
                                   data-page="35"><i class="la la-angle-double-right"></i></a></li>
                        </ul>
                        <div class="m-datatable__pager-info">
                            <span class="m-datatable__pager-detail">1 to 100 of 577</span>
                        </div>
                    </div>
                    <!--/ Pagination -->

                    <?=
                        LinkPager::widget([
                            'pagination' => $pagination,
                        ])
                    ?>

                </div>

            </div>
        </div>


    </div>

</div>

<!-- Order Details modal -->
<div id="suborder-details-modal" class="modal fade order-detail" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
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


<!-- TODO:: Delete scripts after the main script developing is finished  -->
<script src="/js/libs/jquery.js"></script>
<script src="/js/main.js"></script>
<script src="/js/libs/popper.js"></script>
<script src="/js/libs/bootstrap.js"></script>

<!-- TODO:: Move scripts to the module after checking -->
<script>
(function (window, alert) {
    var ajaxEndpoint = '<?= Url::to(['/admin/orders/get-order-details']) ?>';
    var $detailsModal = $('#suborder-details-modal'),
    $modatTitle = $detailsModal.find('.modal-title'),
    $provider = $detailsModal.find('#order-detail-provider'),
    $providerOrderId = $detailsModal.find('#order-detail-provider-order-id'),
    $providerResponce = $detailsModal.find('#order-detail-provider-response'),
    $providerUpdate = $detailsModal.find('#order-detail-lastupdate');

        $detailsModal.on('show.bs.modal', function(e) {
            var suborderId = $(e.relatedTarget).data('suborder-id');
            if (suborderId === undefined || isNaN(suborderId)) {
                return;
            }
            $.ajax({
                url: ajaxEndpoint,
                type: "GET",
                data: {
                    'suborder_id': suborderId
                },
                success: function (data) {
                    if (data.details === undefined) {
                        return;
                    }
                    renderLogs(data.details);
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log('Something is wrong!');
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });

            function renderLogs(details){
                $modatTitle.html('Order ' + suborderId + ' details');
                $provider.val(details.provider);
                $providerOrderId.val(details.provider_order_id);
                $providerResponce.html(details.provider_response);
                $providerUpdate.val(details.updated_at);
            }
        });

        $detailsModal.on('hidden.bs.modal',function(e) {
            var $currentTarget = $(e.currentTarget);
            $currentTarget.find('input').val('');
            $providerResponce.html('');
        });
})({}, function (){})
</script>