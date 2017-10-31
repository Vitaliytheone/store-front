<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $foundOrdersDataProvider frontend\modules\admin\data\OrdersActiveDataProvider */

$this->title = 'Orders';

$formater = Yii::$app->formatter;
$orders = $foundOrdersDataProvider->getOrdersSuborders();
$pagination = $foundOrdersDataProvider->getPagination();


?>
<div class="row">

    <div class="col">

        <div class="row sommerce-block">
            <div class="col-lg-10 col-sm-12">
                <nav class="nav nav-tabs sommerce-tabs__nav" role="tablist">
                    <a class="nav-item nav-link active" id="all-orders-tab" data-toggle="tab" href="#all-orders"
                       role="tab" aria-controls="nav-home" aria-expanded="true">All orders</a>
                    <a class="nav-item nav-link" id="awating-tab" data-toggle="tab" href="#awating" role="tab"
                       aria-controls="nav-profile">Awating <span class="m-badge m-badge--metal m-badge--wide">11</span></a>
                    <a class="nav-item nav-link" id="pending-tab" data-toggle="tab" href="#pending" role="tab"
                       aria-controls="nav-profile">Pending</a>
                    <a class="nav-item nav-link" id="in-progress-tab" data-toggle="tab" href="#in-progress" role="tab"
                       aria-controls="nav-profile">In progress</a>
                    <a class="nav-item nav-link" id="complated-tab" data-toggle="tab" href="#complated" role="tab"
                       aria-controls="nav-profile">Completed</a>
                    <a class="nav-item nav-link" id="canceled-tab" data-toggle="tab" href="#canceled" role="tab"
                       aria-controls="nav-profile">Canceled</a>
                    <a class="nav-item nav-link" id="fail-tab" data-toggle="tab" href="#fail" role="tab"
                       aria-controls="nav-profile">Fail </a>
                    <a class="nav-item nav-link" id="error-tab" data-toggle="tab" href="#error" role="tab"
                       aria-controls="nav-profile">Error <span class="m-badge m-badge--danger">1321</span></a>
                </nav>

            </div>
            <div class="col-lg-2 col-sm-12">
                <div class="input-group m-input-group--air">
                    <input type="text" class="form-control" placeholder="Search for..." aria-label="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button"><span class="fa fa-search"></span></button>
                      </span>
                </div>
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
                                                        <li class="m-nav__item">
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							All (3)
																						</span>
                                                            </a>
                                                        </li>
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							Likes (1)
																						</span>
                                                            </a>
                                                        </li>
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							Followers (0)
																						</span>
                                                            </a>
                                                        </li>
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							Subscribers (2)
																						</span>
                                                            </a>
                                                        </li>
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
                                                        <li class="m-nav__item">
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							All (3)
																						</span>
                                                            </a>
                                                        </li>
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							Auto (1)
																						</span>
                                                            </a>
                                                        </li>
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							Manual (0)
																						</span>
                                                            </a>
                                                        </li>
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
            <div class="tab-pane fade" id="awating" role="tabpanel" aria-labelledby="awating-tab">

                <div class="m_datatable m-datatable m-datatable--default">

                    <table class="table table-sommerce m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th class="max-width-100">Customer</th>
                            <th>Amount</th>
                            <th>Link</th>
                            <th class="sommerce-th__action">
                                <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-left"
                                     data-dropdown-toggle="click" aria-expanded="true">
                                    <a href="#" class="m-dropdown__toggle">
                                        Product
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <li class="m-nav__item">
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							All (0)
																						</span>
                                                            </a>
                                                        </li>
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
                                <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-left"
                                     data-dropdown-toggle="click" aria-expanded="true">
                                    <a href="#" class="m-dropdown__toggle">
                                        Mode
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <li class="m-nav__item">
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							All (0)
																						</span>
                                                            </a>
                                                        </li>
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
                        <tr>
                            <th colspan="100" class="text-center">
                                No orders
                            </th>
                        </tr>
                        </tbody>
                    </table>


                </div>


            </div>
        </div>


    </div>

</div>