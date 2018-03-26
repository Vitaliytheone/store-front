<?php
    /* @var $this yii\web\View */
    /* @var $invoice \common\models\panels\Invoices */
    /* @var $customer \common\models\panels\Customers */
    /* @var $pay2co \common\models\panels\Payments */
    /* @var $paymentsList array */
    /* @var $pgid integer */
    /* @var $payWait boolean */

    use common\models\panels\Invoices;

    $colors = [
        Invoices::STATUS_UNPAID => 'text-danger',
        Invoices::STATUS_PAID => 'text-success',
        Invoices::STATUS_CANCELED => 'text-muted',
    ];

    $invoiceDetails = $invoice->invoiceDetails;
    $total = 0;

    $this->context->addModule('invoiceController', [
        'live' => !$payWait,
        'pgid' => $pgid,
        'notes' => $invoice->getNotesByPaymentMethods()
    ]);
?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2" style="margin-top: 20px">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <h3>
                            <?= Yii::t('app', 'invoices.view.header', [
                                'id' => $invoice->id
                            ])?>
                        </h3>

                        <?= Yii::t('app', 'invoices.view.invoice_date', [
                            'date' => $invoice->getFormattedDate('date', 'php:Y-m-d H:i:s', $customer->timezone)
                        ]); ?>
                        <br />
                        <?= Yii::t('app', 'invoices.view.due_date', [
                            'date' => $invoice->getFormattedDate('expired', 'php:Y-m-d H:i:s', $customer->timezone)
                        ]); ?>

                        <h3 class="<?= $colors[$invoice->status] ?>"><?= $invoice->getStatusName() ?></h3>

                        <?php if ($invoice->can('pay')): ?>
                            <form class="form-inline" action="/checkout/<?= $invoice->code ?>" method="post">
                                <div class="form-group">
                                    <select class="form-control" <?= ($payWait ? 'disabled' : '') ?> name="pgid" id="pgid">
                                        <?php foreach ($paymentsList as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= ($key == $pgid ? 'selected' : '') ?>><?= $value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" <?= ($payWait ? 'disabled' : '') ?> class="btn btn-default"><?= Yii::t('app', 'invoices.view.btn_pay'); ?></button>
                                </div>
                            </form>
                        <?php endif ?>
                    </div>
                </div>

                <div class="row hidden" id="paymentContent">
                    <div class="col-sm-offset-2 col-sm-8" style="margin-top: 20px">
                        <div class="alert <?= ($payWait ? 'alert-warning' : 'alert-info') ?> content"></div>
                    </div>
                </div>

                <div class="row">
                        <div class="col-xs-12" style="margin-top: 20px">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><?= Yii::t('app', 'invoices.view.description_column')?></th>
                                        <th><?= Yii::t('app', 'invoices.view.amount_column')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($invoiceDetails)) : ?>
                                        <?php foreach ($invoiceDetails as $details) : ?>
                                            <?php $total += $details->amount; ?>
                                            <tr>
                                                <td>
                                                    <?= $details->getDescription()?>
                                                </td>
                                                <td>
                                                    $<?= number_format($details->amount, 2)?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <tr>
                                        <td class="text-right"><strong><?= Yii::t('app', 'invoices.view.sub_total')?></strong></td>
                                        <td><strong>$<?= number_format($total, 2)?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><strong><?= Yii::t('app', 'invoices.view.credit')?></strong></td>
                                        <td><strong>$<?= number_format($invoice->credit, 2)?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><strong><?= Yii::t('app', 'invoices.view.total')?></strong></td>
                                        <td><strong>$<?= number_format($invoice->getPaymentAmount(), 2)?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!Yii::$app->user->isGuest) : ?>
            <div class="col-md-8 col-md-offset-2 text-center">
                <a href="/invoices">« <?= Yii::t('app', 'invoices.view.back_to_invoices_link')?></a>
            </div>
        <?php endif ?>
    </div>
</div>
<script type="text/javascript" src="/themes/js/invoice.js"></script>