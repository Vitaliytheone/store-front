<div class="row">

    <div class="col">

        <div class="row sommerce-block">
            <div class="col-lg-10 col-sm-12">
                <nav class="nav nav-tabs sommerce-tabs__nav" role="tablist">
                    <a class="nav-item nav-link active" id="all-orders-tab" data-toggle="tab" href="#all-orders" role="tab" aria-controls="nav-home" aria-expanded="true">All orders</a>
                    <a class="nav-item nav-link" id="awating-tab" data-toggle="tab" href="#awating" role="tab" aria-controls="nav-profile">Awating <span class="m-badge m-badge--metal">2</span></a>
                    <a class="nav-item nav-link" id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="nav-profile">Completed</a>
                    <a class="nav-item nav-link" id="failed-tab" data-toggle="tab" href="#failed" role="tab" aria-controls="nav-failed">Failed  <span class="m-badge m-badge--danger">2</span></a>
                    <a class="nav-item nav-link" id="refunded-tab" data-toggle="tab" href="#refunded" role="tab" aria-controls="nav-refunded">Refunded</a>
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
                            <th>Customer</th>
                            <th>Amount</th>
                            <th class="sommerce-th__action">
                                <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-left" data-dropdown-toggle="click" aria-expanded="true">
                                    <a href="#" class="m-dropdown__toggle">
                                        Method
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							All (2)
																						</span>
                                                            </a>
                                                        </li>
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							PayPal (1)
																						</span>
                                                            </a>
                                                        </li>                                                            <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							2Checkout (1)
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
                            <th>Fee</th>
                            <th>Memo</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="sommerce-th__action-buttons"></th>
                        </tr>
                        </thead>

                        <tbody class="m-datatable__body">
                        <tr>
                            <td>1</td>
                            <td>email@sommerce.lo</td>
                            <td>20.00</td>
                            <td>
                                PayPal
                            </td>
                            <td >0.30</td>
                            <td>IDSJANDUK312312DJJKSADda</td>
                            <td>Completed</td>
                            <td nowrap="">2017-07-31 14:52:23</td>
                            <td class="text-right">
                                <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-right" data-dropdown-toggle="click" aria-expanded="true">
                                    <a href="#" class="m-dropdown__toggle btn btn-primary btn-sm">
                                        Actions <span class="fa fa-cog"></span>
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <li class="m-nav__item">
                                                            <a href="#" data-toggle="modal" data-target=".payments_detail" data-backdrop="static" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							Details
																						</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>


                    <div class="m-datatable__pager m-datatable--paging-loaded clearfix mb-3">
                        <ul class="m-datatable__pager-nav">
                            <li><a title="Primero" class="m-datatable__pager-link m-datatable__pager-link--first m-datatable__pager-link--disabled" data-page="1" disabled="disabled"><i class="la la-angle-double-left"></i></a></li>
                            <li><a title="Anterior" class="m-datatable__pager-link m-datatable__pager-link--prev m-datatable__pager-link--disabled" data-page="1" disabled="disabled"><i class="la la-angle-left"></i></a></li>
                            <li style="display: none;"><a title="Más páginas" class="m-datatable__pager-link m-datatable__pager-link--more-prev" data-page="1"><i class="la la-ellipsis-h"></i></a></li>
                            <li style="display: none;"><input type="text" class="m-pager-input form-control" title="Número de página"></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number m-datatable__pager-link--active" data-page="1">1</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="2">2</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="3">3</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="4">4</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="5">5</a></li>
                            <li style=""><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="6">6</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="7">7</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="8">8</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="9">9</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="10">10</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="11">11</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="12">12</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="13">13</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="14">14</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="15">15</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="16">16</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="17">17</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="18">18</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="19">19</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="20">20</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="21">21</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="22">22</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="23">23</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="24">24</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="25">25</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="26">26</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="27">27</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="28">28</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="29">29</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="30">30</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="31">31</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="32">32</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="33">33</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="34">34</a></li>
                            <li style="display: none;"><a class="m-datatable__pager-link m-datatable__pager-link-number" data-page="35">35</a></li>
                            <li><a title="Más páginas" class="m-datatable__pager-link m-datatable__pager-link--more-next" data-page="7"><i class="la la-ellipsis-h"></i></a></li>
                            <li><a title="Siguiente" class="m-datatable__pager-link m-datatable__pager-link--next" data-page="2"><i class="la la-angle-right"></i></a></li>
                            <li><a title="Último" class="m-datatable__pager-link m-datatable__pager-link--last" data-page="35"><i class="la la-angle-double-right"></i></a></li>
                        </ul>
                        <div class="m-datatable__pager-info">
                            <span class="m-datatable__pager-detail">1 to 100 of 577</span>
                        </div>
                    </div>

                </div>


            </div>
            <div class="tab-pane fade" id="awating" role="tabpanel" aria-labelledby="awating-tab">

                <div class="m_datatable m-datatable m-datatable--default">

                    <table class="table table-sommerce m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th class="sommerce-th__action">
                                <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-left" data-dropdown-toggle="click" aria-expanded="true">
                                    <a href="#" class="m-dropdown__toggle">
                                        Method
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                                        <div class="m-dropdown__inner">
                                            <div class="m-dropdown__body">
                                                <div class="m-dropdown__content">
                                                    <ul class="m-nav">
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							All (2)
																						</span>
                                                            </a>
                                                        </li>
                                                        <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							PayPal (1)
																						</span>
                                                            </a>
                                                        </li>                                                            <li class="m-nav__item">
                                                            <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							2Checkout (1)
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
                            <th>Fee</th>
                            <th>Memo</th>
                            <th>Status</th>
                            <th>Date</th>
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


<div class="modal fade payments_detail" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment 1 details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <pre class="sommerce-pre">
2017-04-28 15:53:42
Array
(
    [middle_initial] =>
    [li_0_name] => Product
    [sid] => 901344558
    [key] => CBB649D87B536F6473BC6A6B503A09FC
    [state] =>
    [email] => test@my.lo
    [_csrf] => cnlQT2VwTEpESTs/D10WHxhBFycTSAY6BiY6P1FBLXgFDTN5JyA8CQ==
    [li_0_type] => product
    [go-button] =>
    [order_number] => 9093735518100
    [currency_code] => USD
    [lang] => en
    [invoice_id] => 9093735518109
    [li_0_price] => 3.00
    [total] => 3.00
    [credit_card_processed] => Y
    [zip] =>
    [li_0_quantity] => 1
    [cart_weight] => 0
    [fixed] => Y
    [last_name] =>
    [li_0_product_id] => 25
    [street_address] => Address
    [city] => City
    [li_0_tangible] =>
    [li_0_description] =>
    [merchant_order_id] =>
    [country] => AGO
    [ip_country] => Ukraine
    [demo] =>
    [pay_method] => CC
    [cart_tangible] => N
    [phone] =>
    [street_address2] => Address
    [x_receipt_link_url] => http://sommerce.lo/payments/success-checkout
    [first_name] => Name
    [card_holder_name] => Name
)
                    </pre>
            </div>
        </div>
    </div>
</div>