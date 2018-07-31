<?php
    /* @var $this yii\web\View */
    /* @var $invoices \my\modules\superadmin\models\search\InvoicesSearch */
    /* @var $invoice \common\models\panels\Invoices */

    use my\helpers\Url;
    use yii\helpers\Html;
    use yii\widgets\LinkPager;
    use common\models\panels\Invoices;
    use my\helpers\PriceHelper;
    use my\helpers\SpecialCharsHelper;
?>
<table class="table table-border">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'invoices.list.column_id')?></th>
        <th><?= Yii::t('app/superadmin', 'invoices.list.column_domain')?></th>
        <th><?= Yii::t('app/superadmin', 'invoices.list.column_total')?></th>
        <th><?= Yii::t('app/superadmin', 'invoices.list.column_credit')?></th>
        <th><?= Yii::t('app/superadmin', 'invoices.list.column_status')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'invoices.list.column_created')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'invoices.list.column_due_date')?></th>
        <th class="w-1"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($invoices['models'])) : ?>
        <?php foreach (SpecialCharsHelper::multiPurifier($invoices['models']) as $key => $invoice) : ?>
            <tr>
                <td>
                    <?= $invoice->id ?>
                </td>
                <td>
                    <?= $invoice->getDomain() ?>
                </td>
                <td>
                    <?= PriceHelper::prepare($invoice->total) ?>
                </td>
                <td>
                    <?= PriceHelper::prepare($invoice->credit) ?>
                </td>
                <td>
                    <?= $invoice->getStatusName() ?>
                </td>
                <td>
                    <span class="text-nowrap">
                        <?= $invoice->getFormattedDate('date', 'php:Y-m-d') ?>
                    </span>
                    <?= $invoice->getFormattedDate('date', 'php:H:i:s') ?>
                </td>
                <td>
                    <span class="text-nowrap">
                        <?= $invoice->getFormattedDate('expired', 'php:Y-m-d') ?>
                    </span>
                    <?= $invoice->getFormattedDate('expired', 'php:H:i:s') ?>
                </td>

                <td>

                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'ssl.list.actions_label')?></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?= Html::tag('span', Yii::t('app/superadmin', 'invoices.list.action_get_link'), [
                                    'class' => 'dropdown-item copy pointer',
                                    'data-link' => Url::to('https://my.perfectpanel.com/invoices/' . $invoice->code),

                                ])?>

                                <?php if (Invoices::STATUS_UNPAID == $invoice->status) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'invoices.list.action_add_payment'), Url::toRoute(['/invoices/add-payment', 'id' => $invoice->id]), [
                                        'class' => 'dropdown-item add-payment',
                                    ])?>
                                <?php endif; ?>

                                <?php if ($invoice->can('editTotal')) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'invoices.list.action_edit'), Url::toRoute(['/invoices/edit', 'id' => $invoice->id]), [
                                        'class' => 'dropdown-item edit-invoice',
                                        'data-details' => $invoice->getAttributes(['total'])
                                    ])?>
                                <?php endif; ?>

                                <?php if (Invoices::STATUS_UNPAID == $invoice->status) : ?>

                                    <?= Html::a(Yii::t('app/superadmin', 'invoices.list.action_edit_credit'), Url::toRoute(['/invoices/edit-credit', 'id' => $invoice->id]), [
                                        'class' => 'dropdown-item edit-credit',
                                        'data-details' => [
                                            'credit' => PriceHelper::prepare($invoice->credit)
                                        ]
                                    ])?>

                                    <?= Html::a(Yii::t('app/superadmin', 'invoices.list.action_cancel'), Url::toRoute(['/invoices/cancel', 'id' => $invoice->id]), [
                                        'class' => 'dropdown-item cancel-menu',
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
        'pagination' => $invoices['pages'],
    ]); ?>
</div>