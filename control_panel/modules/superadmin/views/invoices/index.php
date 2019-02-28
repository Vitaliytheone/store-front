<?php
    /* @var $this yii\web\View */
    /* @var $invoices \superadmin\models\search\InvoicesSearch */
    /* @var $navs \superadmin\models\search\InvoicesSearch */
    /* @var $filters */
    /* @var $status */
    /* @var $searchTypes array */

    use control_panel\helpers\Url;
    use control_panel\helpers\SpecialCharsHelper;
    use yii\helpers\Html;

    $this->context->addModule('superadminInvoicesController');
?>
    <ul class="nav nav-pills mb-3">
        <?php foreach ($navs as $code => $label) : ?>
            <?php $code = is_numeric($code) ? $code : null;?>
            <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute($code === null ? '/invoices' : array_merge(['/invoices'], $filters, ['status' => $code])) ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
        <li class="ml-auto">
            <form class="form" method="GET" id="invoicesSearch" action="<?=Url::toRoute(array_merge(['/invoices'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <a id="createInvoice" class="btn btn-primary" href="<?= Url::toRoute('/invoices/create')?>"><?= Yii::t('app/superadmin', 'invoices.btn.create_new')?></a>&nbsp;
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'invoices.list.search') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">

                    <?= Html::dropDownList('search_type', $filters['search_type'], $searchTypes, [
                            'class' => 'custom-select'
                    ]) ?>
                    <div class="input-group-append">
                        <button class="btn btn-light" type="submit"><span class="fa fa-search"></span></button>
                    </div>
                </div>
            </form>
        </li>
    </ul>

    <?= $this->render('layouts/_invoices_list', [
        'invoices' => $invoices,
        'filters' => $filters,
    ])?>

<?= $this->render('layouts/_add_credit_modal')?>
<?= $this->render('layouts/_add_payment_modal')?>
<?= $this->render('layouts/_edit_invoice_modal')?>
<?= $this->render('layouts/_create_invoice_modal')?>
<?= $this->render('layouts/_add_earnings_modal')?>