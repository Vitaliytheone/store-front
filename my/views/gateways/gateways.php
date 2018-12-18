<?php
/* @var $this yii\web\View */
/* @var $gateways[] \common\models\gateways\Sites */
/* @var $gateway \common\models\gateways\Sites */
/* @var $accesses */

use common\models\gateways\Sites;
use common\models\panels\Orders;
use yii\bootstrap\Html;

$gatewayColors = [
    Sites::STATUS_FROZEN => 'text-danger',
    Sites::STATUS_ACTIVE => 'text-success',
    Sites::STATUS_TERMINATED => 'text-muted',
];

$orderColors = [
    Orders::STATUS_PAID => '',
    Orders::STATUS_ERROR => '',
    Orders::STATUS_PENDING => '',
    Orders::STATUS_CANCELED => 'text-muted',
];

$colors = function($gateway) use ($gatewayColors, $orderColors) {
    if ('order' == $gateway['type']) {
        return $orderColors[$gateway['status']];
    } else {
        return $gatewayColors[$gateway['status']];
    }
};

?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header">
            <?= Yii::t('app', 'sites.list.header')?>
            <a href="/order" class="btn btn-outline btn-success create-order" <?= $accesses['canCreate'] ? '' : 'data-error="Orders limit exceeded."' ?>>
                <?= Yii::t('app', 'sites.list.order_gateway')?>
            </a>
            <div class="alert alert-danger error-hint hidden" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="content"></span>
            </div>
        </h2>
    </div>
</div>
<?php if (!empty($gateways)): ?>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th><?= Yii::t('app', 'sites.list.domain')?></th>
                    <th><?= Yii::t('app', 'sites.list.created')?></th>
                    <th><?= Yii::t('app', 'sites.list.expiry')?></th>
                    <th><?= Yii::t('app', 'sites.list.status')?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($gateways as $gateway): ?>
                    <?php if (!$gateway['hide']): ?>
                        <tr>
                            <td><?= $gateway['domain'] ?></td>
                            <td>
                                <?= $gateway['date']; ?>
                            </td>
                            <td>
                                <?= $gateway['expired']; ?>
                            </td>
                            <td class="<?= $colors($gateway) ?>">
                                <?= $gateway['statusName'] ?>
                            </td>
                            <td>
                                <?php if ($gateway['access']['isActive']) : ?>
                                    <?= Html::a('<i class="fa fa-external-link fa-fw"></i> ' . Yii::t('app', 'sites.list.action_dashboard'), 'http://'. strip_tags($gateway['domain']) . '/admin', [
                                        'class' => 'btn btn-outline btn-primary btn-xs',
                                        'target' => '_blank'
                                    ])?>

                                <?php else : ?>
                                    <?= Html::tag('span', '<i class="fa fa-external-link fa-fw"></i> ' . Yii::t('app', 'sites.list.action_dashboard'), [
                                        'class' => 'btn btn-outline btn-primary btn-xs disabled',
                                        'target' => '_blank'
                                    ])?>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
