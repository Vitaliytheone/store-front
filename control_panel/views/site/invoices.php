<?php
    /* @var $this yii\web\View */
    /* @var $invoices \common\models\sommerces\Invoices */
    /* @var $invoice \common\models\sommerces\Invoices */

    use common\models\sommerces\Invoices;
    use yii\widgets\LinkPager;

    $colors = [
        Invoices::STATUS_UNPAID => 'text-danger',
        Invoices::STATUS_PAID => 'text-success',
        Invoices::STATUS_CANCELED => 'text-muted',
    ];
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"><?= Yii::t('app', 'invoices.list.header')?></h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <?php if (!empty($invoices['models'])): ?>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'invoices.list.invoice_column')?></th>
                        <th><?= Yii::t('app', 'invoices.list.invoice_date_column')?></th>
                        <th><?= Yii::t('app', 'invoices.list.total_column')?></th>
                        <th><?= Yii::t('app', 'invoices.list.status_column')?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices['models'] as $invoice): ?>
                        <tr>
                            <td><?= $invoice->id ?></td>
                            <td>
                                <?= $invoice->getFormattedDate('date', 'php:Y-m-d')?>
                            </td>
                            <td>$<?= $invoice->total ?></td>
                            <td class="<?= $colors[$invoice->status] ?>">
                                <?= $invoice->getStatusName() ?>
                            </td>
                            <td>
                                <a class="btn btn-outline btn-primary btn-xs" href="/invoices/<?= $invoice->code ?>">
                                    <i class="fa fa-file-o fa-fw"></i> <?= Yii::t('app', 'invoices.list.view_invoice_link')?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

        <?php endif ?>

        <div class="text-align-center">
            <?= LinkPager::widget([
                'pagination' => $invoices['pages'],
            ]); ?>
        </div>
    </div>
</div>
  