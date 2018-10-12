<?php
    /* @var $this yii\web\View */
    /* @var $payments \my\modules\superadmin\models\search\PaymentsSearch */
    /* @var $payment \my\modules\superadmin\models\search\PaymentsSearch */
    /* @var $modes */
    /* @var $methods */

    use my\helpers\Url;
    use yii\helpers\Html;
    use yii\widgets\LinkPager;
    use my\helpers\PriceHelper;
    use my\helpers\SpecialCharsHelper;
?>
<table class="table table-border">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'payments.list.column_id')?></th>
        <th><?= Yii::t('app/superadmin', 'payments.list.column_invoice')?></th>
        <th><?= Yii::t('app/superadmin', 'payments.list.column_domain')?></th>
        <th><?= Yii::t('app/superadmin', 'payments.list.column_amount')?></th>
        <th><?= Yii::t('app/superadmin', 'payments.list.column_fee')?></th>
        <th><?= Yii::t('app/superadmin', 'payments.list.column_memo')?></th>
        <th class="text-nowrap">
            <div class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'payments.list.column_method')?></a>
                <div class="dropdown-menu">
                    <?php foreach ($methods as $method => $label) : ?>
                        <a class="dropdown-item <?=($method === $filters['method'] ? 'active' : '')?>" href="<?=Url::toRoute(array_merge(['/payments'], $filters, ['method' => $method]))?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </th>
        <th><?= Yii::t('app/superadmin', 'payments.list.column_status')?></th>
        <th class="text-nowrap">
            <div class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'payments.list.column_mode')?></a>
                <div class="dropdown-menu">
                    <?php foreach ($modes as $mode => $label) : ?>
                        <?php $mode = is_numeric($mode) ? (int)$mode : null ?>
                        <a class="dropdown-item <?=($mode === $filters['mode'] ? 'active' : '')?>" href="<?=Url::toRoute(array_merge(['/payments'], $filters, ['mode' => $mode]))?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </th>

        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'payments.list.column_created')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'payments.list.column_updated')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'payments.list.column_ip')?></th>
        <th class="w-1"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($payments['models'])) : ?>
        <?php foreach (SpecialCharsHelper::multiPurifier($payments['models']) as $key => $payment) : ?>
            <tr>
                <td>
                    <?= $payment->id ?>
                </td>
                <td>
                    <?= Html::a($payment->iid, Url::toRoute(['/invoices', 'id' => $payment->iid])) ?>
                </td>
                <td>
                    <?= $payment->getDomain() ?>
                </td>
                <td>
                    <?= PriceHelper::prepare($payment->amount) ?>
                </td>
                <td>
                    <?= isset($payment->fee) ? PriceHelper::prepare($payment->fee) : '' ?>
                </td>
                <td>
                    <?= $payment->comment ?>
                </td>
                <td>
                    <?= $payment->getMethodName() ?>
                </td>
                <td>
                    <?= $payment->getStatusName() ?>
                </td>
                <td>
                    <?= $payment->getModeName() ?>
                </td>
                <td>
                    <span class="text-nowrap">
                        <?= $payment->getFormattedDate('date', 'php:Y-m-d') ?>
                    </span>
                    <?= $payment->getFormattedDate('date', 'php:H:i:s') ?>
                </td>
                <td>
                    <span class="text-nowrap">
                        <?= $payment->getFormattedDate('date_update', 'php:Y-m-d') ?>
                    </span>
                    <?= $payment->getFormattedDate('date_update', 'php:H:i:s') ?>
                </td>
                <td>
                    <?= $payment->ip ?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'payments.list.actions_label')?></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?= Html::a(Yii::t('app/superadmin', 'payments.list.action_details'), Url::toRoute(['/payments/details', 'id' => $payment->id]), [
                                'class' => 'dropdown-item payment-details',
                            ])?>
                            <?php if ($payment->can('makeActive')) : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'payments.list.action_make_active'), Url::toRoute(['/payments/make-active', 'id' => $payment->id]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>
                            <?php if ($payment->can('makeAccepted')) : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'payments.list.action_make_accepted'), Url::toRoute(['/payments/make-accepted', 'id' => $payment->id]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>
                            <?php if ($payment->can('makeRefunded')) : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'payments.list.action_make_refunded'), '#' , [
                                    'class' => 'dropdown-item',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#paymentRefundModal',
                                    'data-payment_id' => $payment->id,
                                    'data-payment_amount' => $payment->amount,
                                    'data-action_url' => Url::toRoute(['/payments/make-refunded', 'id' => $payment->id]),
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
        'pagination' => $payments['pages'],
    ]); ?>
</div>