<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use frontend\modules\admin\components\Url;
use frontend\helpers\UiHelper;
use frontend\modules\admin\widgets\CustomLinkPager;
use common\models\store\Payments;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel frontend\modules\admin\models\search\PaymentsSearch */

$statusFilterButtons = $searchModel->getStatusFilterButtons([
    Payments::STATUS_AWAITING => [
        'show_count' => true,
        'badge-class' => 'm-badge--metal'
    ],
    Payments::STATUS_FAILED => [
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
                                   href="<?= $button['url'] ?>" id="<?= $button['id'] ?>" aria-controls="nav-home"
                                   aria-expanded="true">
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
                        <form action="<?= Url::toRoute('/payments') ?>">
                            <div class="input-group m-input-group--air">
                                <input type="text" class="form-control" name="query"
                                       value="<?= Html::encode(yii::$app->getRequest()->get('query')) ?>"
                                       placeholder="<?= Yii::t('admin', 'payments.search_placeholder') ?>"
                                       aria-label="<?= Yii::t('admin', 'payments.search_placeholder') ?>">
                                <?php foreach (yii::$app->getRequest()->get() as $param => $value): ?>
                                    <?php if ($param !== 'query'): ?>
                                        <input type="hidden" name="<?= Html::encode($param); ?>"
                                               value="<?= Html::encode($value); ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit"><span class="fa fa-search"></span></button>
                        </span>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="tab-content">
                    <div class="" id="all-orders">

                        <div class="m_datatable m-datatable m-datatable--default">

                            <table class="table table-sommerce m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">

                                <thead>
                                <tr>
                                    <th><?= Yii::t('admin', 'payments.t_id') ?></th>
                                    <th><?= Yii::t('admin', 'payments.t_customer') ?></th>
                                    <th><?= Yii::t('admin', 'payments.t_amount') ?></th>
                                    <th class="sommerce-th__action">
                                        <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-left"
                                             data-dropdown-toggle="click" aria-expanded="true">
                                            <a href="#" class="m-dropdown__toggle">
                                                <?= Yii::t('admin', 'payments.t_method') ?>
                                            </a>
                                            <div class="m-dropdown__wrapper">
                                                <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                                                <div class="m-dropdown__inner">
                                                    <div class="m-dropdown__body">
                                                        <div class="m-dropdown__content">
                                                            <ul class="m-nav">
                                                                <?php foreach ($searchModel->getMethodFilterItems() as $item): ?>
                                                                    <li class="m-nav__item <?= $item['active'] ? 'active' : '' ?>">
                                                                        <a href="<?= $item['url'] ?>" class="m-nav__link">
                                                                            <span class="m-nav__link-text"><?= $item['method_title'] ?> (<?= $item['count'] ?>)</span>
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
                                    <th><?= Yii::t('admin', 'payments.t_fee') ?></th>
                                    <th><?= Yii::t('admin', 'payments.t_memo') ?></th>
                                    <th><?= Yii::t('admin', 'payments.t_status') ?></th>
                                    <th><?= Yii::t('admin', 'payments.t_date') ?></th>
                                    <th class="sommerce-th__action-buttons"></th>
                                </tr>
                                </thead>

                                <tbody class="m-datatable__body">
                                <?php foreach ($searchModel->getPayments() as $payment): ?>
                                    <?= $this->render('_payment_item', ['payment' => $payment]); ?>
                                <?php endforeach; ?>
                                </tbody>

                            </table>

                            <div class="m-datatable__pager m-datatable--paging-loaded clearfix mb-3">
                                <?= CustomLinkPager::widget(['pagination' => $dataProvider->getPagination()]) ?>
                                <div class="m-datatable__pager-info">
                                    <span class="m-datatable__pager-detail"><?= UiHelper::listSummary($dataProvider) ?></span>
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
