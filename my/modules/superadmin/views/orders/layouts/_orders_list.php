<?php
    /* @var $this yii\web\View */
    /* @var $orders \my\modules\superadmin\models\search\OrdersSearch */
    /* @var $order \common\models\panels\Orders */
    /* @var $filters */
    /* @var $items */

    use yii\helpers\Html;
    use common\models\panels\Orders;
    use my\helpers\Url;
    use yii\widgets\LinkPager;
    use my\helpers\SpecialCharsHelper;
    use common\models\panels\Invoices;
?>
<table class="table table-sm table-custom">
    <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'orders.list.column_id') ?></th>
            <th><?= Yii::t('app/superadmin', 'orders.list.column_customer') ?></th>
            <th><?= Yii::t('app/superadmin', 'orders.list.column_invoice') ?></th>
            <th class="table-custom__dropdown">
                <div class="dropdown">
                    <a class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'orders.list.column_item') ?></a>
                    <div class="dropdown-menu dropdown-menu__max">
                        <?php foreach ($items as $item => $label) : ?>
                            <a class="dropdown-item <?=($item === (int)$filters['item'] ? 'active' : '')?>" href="<?=Url::toRoute(array_merge(['/orders'], $filters, ['item' => $item]))?>"><?= $label ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </th>
            <th><?= Yii::t('app/superadmin', 'orders.list.column_domain') ?></th>
            <th><?= Yii::t('app/superadmin', 'orders.list.column_status') ?></th>
            <th class="text-nowrap"><?= Yii::t('app/superadmin', 'orders.list.column_created') ?></th>
            <th class="text-nowrap"><?= Yii::t('app/superadmin', 'orders.list.column_ip') ?></th>
            <th class="table-custom__action-th"></th>
        </tr>
    </thead>
    <tbody>
            <?php if (!empty($orders['models'])) : ?>
            <?php foreach (SpecialCharsHelper::multiPurifier($orders['models']) as $order) : ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td>
                        <?= Html::a(SpecialCharsHelper::multiPurifier($order['customer_email']), Url::toRoute("/customers#" . $order['customer_id'])); ?>
                    </td>
                    <td>
                        <?php if (!empty($order['invoice_id'])) : ?>
                            <?= Html::a($order['invoice_id'], Url::toRoute("/invoices?id=" . $order['invoice_id'])); ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $order['item'] ?></td>
                    <td class="text-nowrap"><?= $order['domain'] ?></td>
                    <td><?= $order['status'] ?></td>
                    <td>
                        <span class="text-nowrap">
                            <?= $order['date'] ?>
                        </span>
                        <?= $order['time'] ?>
                    </td>
                    <td>
                        <?= $order['ip'] ?>
                    </td>
                    <td>
                        <?php
                            $showMark = in_array($order['check_status'], [
                                Orders::STATUS_ERROR,
                                Orders::STATUS_PENDING,
                            ]);
                        ?>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'orders.list.dropdown_actions') ?></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?= Html::a(Yii::t('app/superadmin', 'orders.list.dropdown_item_order_details'), Url::toRoute(['/orders/details', 'id' => $order['id']]), [
                                    'class' => 'dropdown-item order-details',
                                ])?>

                                <?php if ($showMark) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'orders.list.dropdown_item_mark_as_ready'), Url::toRoute('/orders/change-status'), [
                                        'class' => 'dropdown-item order-status',
                                        'data-method' => 'POST',
                                        'data-params' => ['id' => $order['id'], 'status' => Orders::STATUS_PAID],
                                    ])?>
                                <?php endif; ?>

                                <?php if (Orders::STATUS_ERROR == $order['check_status']) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'orders.list.dropdown_item_mark_as_completed'), Url::toRoute('/orders/change-status'), [
                                        'class' => 'dropdown-item order-status',
                                        'data-method' => 'POST',
                                        'data-params' => ['id' => $order['id'], 'status' => Orders::STATUS_ADDED],
                                    ])?>
                                <?php endif; ?>

                                <?php if (Orders::STATUS_PENDING == $order['check_status']) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'orders.list.dropdown_item_cancel'), Url::toRoute('/orders/change-status'), [
                                        'class' => 'dropdown-item cancel-menu',
                                        'data-method' => 'POST',
                                        'data-params' => [
                                            'id' => $order['id'],
                                            'status' => Orders::STATUS_CANCELED,
                                        ],
                                        'data-confirm-message' => Yii::t('app/superadmin', 'invoices.list.action_cancel_confirm_message')
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

<div class="text-align-center pager">
    <?= LinkPager::widget([
        'pagination' => $orders['pages'],
    ]); ?>
</div>