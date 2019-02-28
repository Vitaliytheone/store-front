<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $order array */
/* @var $ordersSearchModel store\modules\admin\models\search\OrdersSearch */

$suborders = $order['suborders'];
$subordersCnt = count($suborders);

// Collecting current filters values for redirecting
$currentFilters = yii::$app->getRequest()->get();

// Check requires rowspan for multiple suborders order
$checkRowSpan = function($suborder) use ($suborders) {
    $subordersCount = count($suborders);
    $firstSuborder = array_values($suborders)[0];
    $isFirst = (ArrayHelper::getValue($suborder, 'suborder_id') === ArrayHelper::getValue($firstSuborder, 'suborder_id'));
    return (($subordersCount > 1) && $isFirst) ? $subordersCount : null;
}
?>

<?php foreach ($suborders as $suborder): ?>
<tr>
    <?php $rowSpan = $checkRowSpan($suborder) ?>
    <?php if($rowSpan): ?>
        <td rowspan="<?= $rowSpan ?>"><?= $order['id'] ?></td>
        <td rowspan="<?= $rowSpan ?>"><?= Html::encode($order['customer']) ?></td>
    <?php elseif (count($suborders) == 1): ?>
        <td><?= $order['id'] ?></td>
        <td><?= Html::encode($order['customer']) ?></td>
    <?php endif; ?>

    <td><?= $suborder['amount'] ?></td>
    <td>
        <div class="sommerce-table__link" id="copy-link-<?= $suborder['suborder_id'] ?>">
            <?= Html::encode($suborder['link']) ?>
            <span class="la la-copy" data-clipboard="true" data-clipboard-target="#copy-link-<?= $suborder['suborder_id'] ?>"></span>
        </div>
    </td>
    <td><?= $suborder['product_name'] ?></td>
    <td><?= $suborder['quantity'] ?></td>
    <td><?= $suborder['status_title'] ?></td>

    <?php if ($rowSpan): ?>
        <td rowspan="<?= $rowSpan ?>" nowrap="" class="sommerce-table__no-wrap"><?= $order['created_at'] ?></td>
    <?php elseif (count($suborders) == 1): ?>
        <td nowrap="" class="sommerce-table__no-wrap"><?= $order['created_at'] ?></td>
    <?php endif; ?>

    <td><?= $suborder['mode_title'] ?></td>
    <td class="text-right">

        <?php if(ArrayHelper::getValue($suborder, 'action_menu')): ?>
        <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-right" data-dropdown-toggle="click" aria-expanded="true">
            <a href="#" class="m-dropdown__toggle btn btn-primary btn-sm">
                <?= Yii::t('admin', 'orders.action_title') ?> <span class="fa fa-cog"></span>
            </a>
            <div class="m-dropdown__wrapper">
                <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                <div class="m-dropdown__inner">
                    <div class="m-dropdown__body">
                        <div class="m-dropdown__content">
                            <ul class="m-nav">

                                <?php if(ArrayHelper::getValue($suborder,'action_menu.details')): ?>
                                <li class="m-nav__item">
                                    <a href="#" data-toggle="modal" data-target=".order-detail" data-backdrop="static" class="m-nav__link" data-suborder-id="<?= $suborder['suborder_id'] ?>" data-modal_title="<?= Yii::t('admin', 'orders.details_title', ['suborder_id' => $suborder['suborder_id']]) ?>">
                                        <span class="m-nav__link-text">
                                            <?= Yii::t('admin', 'orders.action_details') ?>
                                        </span>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if(ArrayHelper::getValue($suborder, 'action_menu.resend')): ?>
                                <li class="m-nav__item">
                                    <a href="<?= Url::to(['/admin/orders/resend', 'id'=>$suborder['suborder_id'], 'filters' => $currentFilters]); ?>" class="m-nav__link">
                                        <span class="m-nav__link-text">
                                            <?= Yii::t('admin', 'orders.action_resend') ?>
                                        </span>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php $statusItems = ArrayHelper::getValue($suborder, 'action_menu.status') ?>
                                <?php if(ArrayHelper::getValue($suborder, 'action_menu.status')): ?>
                                <li class="m-nav__item">
                                    <a class="m-nav__link dropdown-collapse dropdown-toggle" data-toggle="collapse" href="#action-<?= $suborder['suborder_id'] ?>">
                                        <span class="m-nav__link-text">
                                            <?= Yii::t('admin', 'orders.action_change_status') ?>
                                        </span>
                                    </a>
                                    <div class="collapse sommerce-dropdwon__actions_collapse" id="action-<?= $suborder['suborder_id'] ?>">
                                        <ul>
                                            <?php foreach ($statusItems as $status): ?>
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal-alert-status" data-backdrop="static" class="change-status" data-action_url="<?= Url::to(['/admin/orders/change-status', 'id' => $suborder['suborder_id'], 'status' => $status['status'], 'filters' => $currentFilters]) ?>">
                                                    <?= $status['status_title'] ?>
                                                </a>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>
                                <?php endif; ?>

                                <?php if (ArrayHelper::getValue($suborder, 'action_menu.cancel')): ?>
                                <li class="m-nav__item">
                                    <a href="#" data-toggle="modal" data-target="#modal-alert-cancel" data-backdrop="static" class="m-nav__link" data-action_url="<?= Url::to(['/admin/orders/cancel', 'id'=>$suborder['suborder_id'], 'filters' => $currentFilters]) ?>">
                                        <span class="m-nav__link-text">
                                            <?= Yii::t('admin', 'orders.action_cancel') ?>
                                        </span>
                                    </a>
                                </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </td>
</tr>
<?php endforeach; ?>

