<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use frontend\modules\admin\components\Url;
use frontend\helpers\Ui;
use frontend\modules\admin\widgets\CustomLinkPager;
use common\models\store\Payments;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel frontend\modules\admin\models\search\PaymentsSearch */

$this->title = Yii::t('admin', 'payments.page_title');

$formatter = Yii::$app->formatter;

$payments = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();

/** Payments items data */

/* Populate payments list by additional formats and data */
array_walk($payments, function(&$payment) use ($formatter){
    $method = $payment['method'];
    $status = $payment['status'];

    $payment['method_title'] = Yii::t('admin', "payments.payment_method_title_$method");
    $payment['status_title'] = Yii::t('admin', "payments.payment_status_$status");
    $payment['updated_at_formatted'] = $formatter->asDatetime($payment['updated_at'],'yyyy-MM-dd HH:mm:ss');
});

/** Status filter buttons */

$statusFilterButtons = [
    -1 => [
        'title' => $this->title = Yii::t('admin', 'payments.filter_all'),
        'stat' => false,
        'stat-class' => 'm-badge m-badge--metal m-badge--wide',
    ],

    Payments::STATUS_AWAITING => [
        'title' => $this->title = Yii::t('admin', 'payments.filter_status_awaiting'),
        'stat' => true,
        'stat-class' => 'm-badge m-badge--metal m-badge--wide',
    ],

    Payments::STATUS_COMPLETED => [
        'title' => $this->title = Yii::t('admin', 'payments.filter_status_completed'),
        'stat' => false,
        'stat-class' => 'm-badge m-badge--metal m-badge--wide',
    ],

    Payments::STATUS_FAILED => [
        'title' => $this->title = Yii::t('admin', 'payments.filter_status_failed'),
        'stat' => true,
        'stat-class' => 'm-badge m-badge--danger',
    ],

    Payments::STATUS_REFUNDED => [
        'title' => $this->title = Yii::t('admin', 'payments.filter_status_refunded'),
        'stat' => false,
        'stat-class' => 'm-badge m-badge--metal m-badge--wide',
    ],
];

$countsPaymentsByStatus = $searchModel->countsByStatus();

/* Populate status filter buttons by additional data */
array_walk($statusFilterButtons, function(&$button, $status) use ($countsPaymentsByStatus){
    /* populate by payments counter */
    $stat = ArrayHelper::getValue($button,'stat', null);

    if ($stat) {
        // All
        if ($status === -1) {
            $counter = array_sum(array_column($countsPaymentsByStatus,'count'));
        } else {
            $counter = ArrayHelper::getValue($countsPaymentsByStatus, "$status.count", 0);
        }
        $button['count'] = $counter;
    }

    /* Populate by action url */
    $buttonUrl = $status === -1 ? Url::toRoute('/payments') : Url::current(['status' => $status], false);
    $button['url'] = $buttonUrl;

    /* Populate by active class */
    $button['active'] = Ui::isFilterActive('status', $status);
});


/** Methods filter menu */

$methodsFilterMenuItems = $searchModel->countsByMethods();

/* Populate methods filter by additional data */
array_walk($methodsFilterMenuItems, function(&$menuItem){
    $method = $menuItem['method'];

    $menuItem['url'] = Url::current(['method' => $method]);
    $menuItem['active'] = Ui::isFilterActive('method', $method);
    $menuItem['method_title'] = Yii::t('admin', "payments.payment_method_title_$method");
});

$allMethodsMenuItem = [
    'method' => -1,
    'method_title' => Yii::t('admin', "payments.payment_method_title_all"),
    'active' =>  Ui::isFilterActive('method', -1),
    'count' => array_sum(array_column($methodsFilterMenuItems,'count')),
    'url' => Url::current(['method' => null]),
];

array_unshift($methodsFilterMenuItems, $allMethodsMenuItem);

?>

<div class="row">

    <div class="col">

        <div class="row sommerce-block">
            <div class="col-lg-10 col-sm-12">

                <nav class="nav nav-tabs sommerce-tabs__nav">

                    <?php foreach ($statusFilterButtons as $status => $button): ?>
                        <a class="<?= $button['active'] ?> nav-item nav-link" id="all-orders-tab" aria-controls="nav-home" aria-expanded="true"
                           href="<?= $button['url'] ?>"
                        >
                            <?= $button['title']?>
                            <?php if ($button['stat']): ?>
                                <span class="<?= $button['stat-class']?>"><?= $button['count'] ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>

                </nav>

            </div>

            <!-- Search -->
            <div class="col-lg-2 col-sm-12">
                <form action="<?= Url::toRoute('/payments') ?>">
                    <div class="input-group m-input-group--air">
                        <input type="text" class="form-control"
                               name="query"
                               value="<?= Html::encode(yii::$app->getRequest()->get('query')) ?>"
                               placeholder="<?= Yii::t('admin', 'payments.search_placeholder') ?>"
                               aria-label="<?= Yii::t('admin', 'payments.search_placeholder') ?>"
                        >
                        <?php foreach (yii::$app->getRequest()->get() as $param => $value): ?>
                            <?php if ($param !== 'query'): ?>
                                <input type="hidden" name="<?= Html::encode($param); ?>" value="<?= Html::encode($value); ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit"><span class="fa fa-search"></span></button>
                        </span>
                    </div>
                </form>
            </div>
            <!--/ Search -->

        </div>

        <div class="tab-content">
            <div class="" id="all-orders">

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
                                                <!--  Method filter  -->
                                                    <ul class="m-nav">
                                                    <?php foreach($methodsFilterMenuItems as $item): ?>
                                                        <li class="m-nav__item">
                                                            <a href="<?= $item['url'] ?>" class="m-nav__link">
                                                                <span class="m-nav__link-text">
                                                                    <?= $item['method_title'] ?>
                                                                    (<?= $item['count'] ?>)
                                                                </span>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                    </ul>
                                                <!--/ Method filter  -->
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
                        <?php foreach ($payments as $payment): ?>
                            <?= $this->render('_payment_item', ['payment' => $payment]); ?>
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
                            <span class="m-datatable__pager-detail"><?= Ui::listSummary($dataProvider) ?></span>
                        </div>
                    </div>
                    <!--/ Pagination -->

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_modal_details', []); ?>
