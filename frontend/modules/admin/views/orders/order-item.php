<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $order array */

$formatter = Yii::$app->formatter;
$suborders = $order['suborders'];
$subordersCnt = count($suborders);

// Get first suborder and remove it from suborders array
//$firstSuborder = array_shift($order['suborders']);

/**
 * Check if $suborder is a first element in $suborders array
 * @param $suborder
 * @return bool
 */
$isFirstSuborder = function($suborder) use ($suborders) {
    return (count($suborders) > 1) && ($suborder == $suborders[0]);
}

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
                                    <a href="#" data-suborder-id="<?= $suborder['suborder_id'] ?>" data-toggle="modal" data-target=".order-detail" data-backdrop="static" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							Details
																						</span>
                                    </a>
                                </li>
                                <li class="m-nav__item">
                                    <a class="m-nav__link dropdown-collapse dropdown-toggle" data-toggle="collapse" href="#action-1">
                                        <span class="m-nav__link-text">Change status</span>
                                    </a>
                                    <div class="collapse sommerce-dropdwon__actions_collapse" id="action-1">
                                        <ul>
                                            <li><a href="#">In progress</a></li>
                                            <li><a href="#">Completed</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="m-nav__item">
                                    <a href="#" class="m-nav__link">
                                                                    <span class="m-nav__link-text">
																							Cancel
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
<?php endforeach; ?>

