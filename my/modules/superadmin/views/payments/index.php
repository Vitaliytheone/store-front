<?php
    /* @var $this yii\web\View */
    /* @var $payments \my\modules\superadmin\models\search\PaymentsSearch */
    /* @var $navs \my\modules\superadmin\models\search\PaymentsSearch */
    /* @var $filters */
    /* @var $status */
    /* @var $modes */
    /* @var $methods */

    use my\helpers\Url;

    $this->context->addModule('superadminPaymentsController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <?php $code = is_numeric($code) ? $code : null;?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(array_merge(['/payments'], $filters, ['status' => $code])) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <form class="form-inline" method="GET" id="paymentsSearch" action="<?=Url::toRoute(array_merge(['/payments'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'payments.list.search') ?>" value="<?=$filters['query']?>">
                    <span class="input-group-btn">
                    <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                </span>
                </div>
            </form>
        </li>
    </ul>

    <?= $this->render('layouts/_payments_list', [
        'payments' => $payments,
        'filters' => $filters,
        'modes' => $modes,
        'methods' => $methods
    ])?>
</div>

<?= $this->render('layouts/_payment_details_modal')?>
<?= $this->render('layouts/_payment_refund_modal')?>