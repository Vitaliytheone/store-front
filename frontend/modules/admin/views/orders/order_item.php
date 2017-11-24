<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use \common\models\store\Suborders;

/* @var $this yii\web\View */
/* @var $order array */
/* @var $ordersSearchModel frontend\modules\admin\models\search\OrdersSearch */
/* @var $formatter yii\i18n\Formatter */

$formatter = Yii::$app->formatter;
$suborders = $order['suborders'];
$subordersCnt = count($suborders);

$allowedActions = $ordersSearchModel::allowedActionStatuses();
$disallowedCancelAction = $ordersSearchModel::$disallowedCancelStatuses;
$disallowedChangeStatusAction = $ordersSearchModel::$disallowedChangeStatusStatuses;
$disallowedDetailsStatusesAction = $ordersSearchModel::$disallowedDetailsStatuses;


/**
 * Check if $suborder is a first element in $suborders array
 * @param $suborder
 * @return bool
 */
$isFirstSuborder = function($suborder) use ($suborders) {
    return (count($suborders) > 1) && ($suborder == array_values($suborders)[0]);
};


/**
 * Return only allowed for action statuses
 * Current model status excluded from action menu.
 * @param $currentStatus
 * @return array
 */
$actionAllowedStatuses = function($currentStatus) use ($allowedActions) {
    if (!isset($allowedActions[$currentStatus])) {
        return $allowedActions;
    }
    unset($allowedActions[$currentStatus]);
    return $allowedActions;
};

/**
 * Show or not model`s `Change status` menu
 * @param $currentStatus
 * @return bool
 */
$isStatusMenuShow = function($currentStatus) use ($disallowedChangeStatusAction) {
    return !in_array($currentStatus, $disallowedChangeStatusAction);
};

/**
 * Show or not model`s `Resend order` menu
 * @param $currentStatus int
 * @return int
 */
$isResendOrderMenuShow = function($currentStatus) {
    return $currentStatus == Suborders::STATUS_FAILED;
};

/**
 * Show or not model`s `Details` menu
 * @param $currentStatus
 * @param $currentMode
 * @return bool
 */
$isDetailsMenuShow = function($currentStatus, $currentMode) use ($disallowedDetailsStatusesAction) {
    return $currentMode == Suborders::MODE_AUTO && !in_array($currentStatus, $disallowedDetailsStatusesAction);
};

/**
 * Show or hide Cancel suborder button
 * @param $currentStatus
 * @return string
 */
$isCancelShow = function($currentStatus) use ($disallowedCancelAction) {
    return $currentStatus != in_array($currentStatus, $disallowedCancelAction);
};

/**
 * Show or not models 'Action' button exactly
 * @param $suborder
 * @return bool
 */
$isActionButtonShow = function($suborder) use ($isStatusMenuShow, $isResendOrderMenuShow, $isDetailsMenuShow, $isCancelShow) {
    $status = $suborder['status'];
    $mode = $suborder['mode'];
    
    return $isStatusMenuShow($status) || $isResendOrderMenuShow($status) || $isDetailsMenuShow($status, $mode) || $isCancelShow($status);
};

/**
 * Collecting current filters values for redirecting
 * @param array $paramNames
 * @return array
 */
$paramsForRedirect = function($paramNames = ['status', 'mode', 'product', 'query']) {
    if (!is_array($paramNames)) {
        $paramNames = [$paramNames];
    }

    $res = [];
    foreach ($paramNames AS $paramName) {
        $param = yii::$app->getRequest()->get($paramName);
        if ($param) {
            $res[$paramName] = $param;
        }
    }
    return $res;
};

?>

<?php foreach ($suborders as $suborder): ?>
<tr>
    <?php if($isFirstSuborder($suborder)): ?>
        <td rowspan="<?= $subordersCnt ?>"><?= $order['id'] ?></td>
        <td rowspan="<?= $subordersCnt ?>"><?= Html::encode($order['customer']) ?></td>
    <?php elseif ($subordersCnt == 1): ?>
        <td><?= $order['id'] ?></td>
        <td><?= Html::encode($order['customer']) ?></td>
    <?php endif; ?>

    <td><?= $suborder['amount'] ?></td>
    <td>
        <div class="sommerce-table__link" id="copy-link-1">
            <?= Html::encode($suborder['link']) ?>
            <span class="la la-copy" data-clipboard="true" data-clipboard-target="#copy-link-1"></span>
        </div>
    </td>
    <td><?= $suborder['product_name'] ?></td>
    <td><?= $suborder['quantity'] ?></td>
    <td><?= $suborder['status_caption'] ?></td>

    <?php if($isFirstSuborder($suborder)): ?>
        <td rowspan="<?= $subordersCnt ?>" nowrap="" class="sommerce-table__no-wrap"><?= $formatter->asDatetime($order['created_at'],'yyyy-MM-dd HH:mm:ss'); ?></td>
    <?php elseif ($subordersCnt == 1): ?>
        <td nowrap="" class="sommerce-table__no-wrap"><?= $formatter->asDatetime($order['created_at'],'yyyy-MM-dd HH:mm:ss'); ?></td>
    <?php endif; ?>

    <td><?= $suborder['mode_caption'] ?></td>
    <td class="text-right">

        <?php if($isActionButtonShow($suborder)): ?>
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

                                <?php if($isDetailsMenuShow($suborder['status'], $suborder['mode'])): ?>
                                <li class="m-nav__item">
                                    <a href="#"
                                       data-toggle="modal"
                                       data-target=".order-detail"
                                       data-backdrop="static"
                                       class="m-nav__link"
                                       data-suborder-id="<?= $suborder['suborder_id'] ?>"
                                       data-modal_title="<?= Yii::t('admin', 'orders.details_title', ['suborder_id' => $suborder['suborder_id']]) ?>"
                                    >
                                        <span class="m-nav__link-text">
                                            <?= Yii::t('admin', 'orders.action_details') ?>
                                        </span>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if($isResendOrderMenuShow($suborder['status'])): ?>
                                <li class="m-nav__item">
                                    <a href="<?= Url::to(['/admin/orders/resend', 'id'=>$suborder['suborder_id'], 'filters' => $paramsForRedirect()]); ?>" class="m-nav__link">
                                        <span class="m-nav__link-text">
                                            <?= Yii::t('admin', 'orders.action_resend') ?>
                                        </span>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <!-- Change Status Menu -->
                                <?php if($isStatusMenuShow($suborder['status'])): ?>
                                <li class="m-nav__item">
                                    <a class="m-nav__link dropdown-collapse dropdown-toggle" data-toggle="collapse" href="#action-<?= $suborder['suborder_id'] ?>">
                                        <span class="m-nav__link-text">
                                            <?= Yii::t('admin', 'orders.action_change_status') ?>
                                        </span>
                                    </a>
                                    <div class="collapse sommerce-dropdwon__actions_collapse" id="action-<?= $suborder['suborder_id'] ?>">
                                        <ul>
                                            <?php foreach ($actionAllowedStatuses($suborder['status']) as $status => $statusData): ?>
                                                <li><a href="<?= Url::to(['/admin/orders/change-status', 'id' => $suborder['suborder_id'], 'status' => $status, 'filters' => $paramsForRedirect()]) ?>" class="change-status"><?= $statusData['caption'] ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>
                                <?php endif; ?>

                                <!--/ Change Status Menu -->
                                <?php if ($isCancelShow($suborder['status'])): ?>
                                <li class="m-nav__item">
                                    <a href="<?= Url::to(['/admin/orders/cancel', 'id'=>$suborder['suborder_id'], 'filters' => $paramsForRedirect()]); ?>" class="m-nav__link">
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

