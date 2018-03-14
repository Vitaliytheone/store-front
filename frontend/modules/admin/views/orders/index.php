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

<div class="page-container">
    <div class="m-container-sommerce container-fluid">
        <div class="row">
            <div class="col">
                <div class="row sommerce-block">

                    <div class="col-lg-10 col-sm-12">
                        <nav class="nav nav-tabs sommerce-tabs__nav">
                            <?php foreach ($statusFilterButtons as $button): ?>
                                <a class="<?= $button['active'] ? 'active' : '' ?> nav-item nav-link"
                                   href="<?= $button['url'] ?>" id="<?= $button['id'] ?>">
                                    <?= $button['title'] ?>
                                    <?php if (ArrayHelper::getValue($button, 'options.show_count') && $button['count'] > 0): ?>
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
                                <input type="text" class="form-control" name="query"
                                       value="<?= Html::encode(yii::$app->getRequest()->get('query')) ?>"
                                       placeholder="<?= Yii::t('admin', 'orders.search_placeholder') ?>"
                                       aria-label="<?= Yii::t('admin', 'orders.search_placeholder') ?>">
                                <?php foreach (yii::$app->getRequest()->get() as $param => $value): ?>
                                    <?php if ($param !== 'query'): ?>
                                        <input type="hidden" name="<?= Html::encode($param); ?>"
                                               value="<?= Html::encode($value); ?>">
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
                    <div class="tab-pane fade show active" id="all-orders" role="tabpanel"
                         aria-labelledby="all-orders-tab">

                        <div class="m_datatable m-datatable m-datatable--default">

                            <table class="table table-sommerce m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                                <thead>
                                <tr>
                                    <th><?= Yii::t('admin', 'orders.t_id') ?></th>
                                    <th class="max-width-100"><?= Yii::t('admin', 'orders.t_customer') ?></th>
                                    <th><?= Yii::t('admin', 'orders.t_amount') ?></th>
                                    <th><?= Yii::t('admin', 'orders.t_link') ?></th>
                                    <th class="sommerce-th__action">
                                        <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-center"
                                             data-dropdown-toggle="click" aria-expanded="true">
                                            <a href="#" class="m-dropdown__toggle">
                                                <?= Yii::t('admin', 'orders.t_product') ?>
                                            </a>
                                            <div class="m-dropdown__wrapper">
                                                <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                                                <div class="m-dropdown__inner">
                                                    <div class="m-dropdown__body">
                                                        <div class="m-dropdown__content">
                                                            <ul class="m-nav">
                                                                <?php foreach ($ordersSearchModel->productFilterItems() as $item): ?>
                                                                    <li class="<?= $item['active'] ? 'active' : '' ?> m-nav__item">
                                                                        <a class="m-nav__link" href="<?= $item['url'] ?>">
                                                                            <span class="m-nav__link-text"><?= $item['name'] ?> (<?= $item['count'] ?>)</span>
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
                                    <th><?= Yii::t('admin', 'orders.t_quantity') ?></th>
                                    <th><?= Yii::t('admin', 'orders.t_status') ?></th>
                                    <th><?= Yii::t('admin', 'orders.t_date') ?></th>
                                    <th class="sommerce-th__action">
                                        <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-center"
                                             data-dropdown-toggle="click" aria-expanded="true">
                                            <a href="#" class="m-dropdown__toggle">
                                                <?= Yii::t('admin', 'orders.t_mode') ?>
                                            </a>
                                            <div class="m-dropdown__wrapper">
                                                <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                                                <div class="m-dropdown__inner">
                                                    <div class="m-dropdown__body">
                                                        <div class="m-dropdown__content">
                                                            <ul class="m-nav">
                                                                <?php foreach ($ordersSearchModel->modeFilterItems() as $modeItem): ?>
                                                                    <li class="<?= $modeItem['active'] ? 'active' : '' ?> m-nav__item">
                                                                        <a href="<?= $modeItem['url'] ?>" class="m-nav__link">
                                                                            <span class="m-nav__link-text"><?= $modeItem['title'] ?> (<?= $modeItem['count'] ?>)</span>
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
                                    <span class="m-datatable__pager-detail"><?= UiHelper::listSummary($ordersDataProvider) ?></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->render('_modal_details', []); ?>
<?= $this->render('_modal_change_status', []); ?>
<?= $this->render('_modal_cancel_order', []); ?>
