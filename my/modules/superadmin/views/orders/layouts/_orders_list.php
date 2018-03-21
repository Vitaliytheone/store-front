<?php
    /* @var $this yii\web\View */
    /* @var $orders \my\modules\superadmin\models\search\OrdersSearch */
    /* @var $order \common\models\panels\Orders */
    /* @var $filters */
    /* @var $items */

    use yii\helpers\Html;
    use common\models\panels\Orders;
    use my\helpers\Url;
?>
<table class="table table-border">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Invoice</th>
            <th class="text-nowrap">
                <div class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Item</a>
                    <div class="dropdown-menu">
                        <?php foreach ($items as $item => $label) : ?>
                            <a class="dropdown-item <?=($item === (int)$filters['item'] ? 'active' : '')?>" href="<?=Url::toRoute(array_merge(['/orders'], $filters, ['item' => $item]))?>"><?= $label ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </th>
            <th>Domain</th>
            <th>Status</th>
            <th class="text-nowrap">Created</th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($orders['models'])) : ?>
            <?php foreach ($orders['models'] as $order) : ?>
                <?php $invoice = $order->invoice; ?>
                <tr>
                    <td><?= $order->id ?></td>
                    <td>
                        <?= Html::a($order->customer->email, Url::toRoute("/customers#" . $order->customer->id)); ?>
                    </td>
                    <td>
                        <?php if (!empty($invoice)) : ?>
                            <?= Html::a($invoice->id, Url::toRoute("/invoices?id=" . $invoice->id)); ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $order->getItemName() ?></td>
                    <td class="text-nowrap"><?= $order->getDomain() ?></td>
                    <td><?= $order->getStatusName() ?></td>
                    <td>
                        <span class="text-nowrap">
                            <?= $order->getFormattedDate('date', 'php:Y-m-d') ?>
                        </span>
                        <?= $order->getFormattedDate('date', 'php:H:i:s') ?>
                    </td>
                    <td>
                        <?php
                            $showMark = in_array($order->status, [
                                Orders::STATUS_ERROR,
                                Orders::STATUS_PENDING,
                            ]);
                        ?>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?= Html::a('Order details', Url::toRoute(['/orders/details', 'id' => $order->id]), [
                                    'class' => 'dropdown-item order-details',
                                ])?>

                                <?php if ($showMark) : ?>
                                    <?= Html::a('Mark as Ready', Url::toRoute(['/orders/change-status', 'id' => $order->id, 'status' => Orders::STATUS_PAID]), [
                                        'class' => 'dropdown-item order-status',
                                    ])?>
                                <?php endif; ?>

                                <?php if (Orders::STATUS_ERROR == $order->status) : ?>
                                    <?= Html::a('Mark as Сompleted', Url::toRoute(['/orders/change-status', 'id' => $order->id, 'status' => Orders::STATUS_ADDED]), [
                                        'class' => 'dropdown-item order-status',
                                    ])?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

    </tbody>
</table>