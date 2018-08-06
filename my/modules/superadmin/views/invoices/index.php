<?php
    /* @var $this yii\web\View */
    /* @var $invoices \my\modules\superadmin\models\search\InvoicesSearch */
    /* @var $navs \my\modules\superadmin\models\search\InvoicesSearch */
    /* @var $filters */
    /* @var $status */

    use my\helpers\Url;
    use my\helpers\SpecialCharsHelper;

    $this->context->addModule('superadminInvoicesController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <?php $code = is_numeric($code) ? $code : null;?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute($code === null ? '/invoices' : array_merge(['/invoices'], $filters, ['status' => $code])) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <a id="createInvoice" class="btn btn-secondary" href="<?= Url::toRoute('/invoices/create')?>"><?= Yii::t('app/superadmin', 'invoices.btn.create_new')?></a>&nbsp;
        </li>
        <li>
            <form class="form-inline" method="GET" id="invoicesSearch" action="<?=Url::toRoute(array_merge(['/invoices'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'invoices.list.search') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>

    <?= $this->render('layouts/_invoices_list', [
        'invoices' => $invoices,
        'filters' => $filters,
    ])?>
</div>
<?= $this->render('layouts/_edit_credit_modal')?>
<?= $this->render('layouts/_add_payment_modal')?>
<?= $this->render('layouts/_edit_invoice_modal')?>
<?= $this->render('layouts/_create_invoice_modal')?>
