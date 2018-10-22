<?php
    /* @var $this yii\web\View */
    /* @var $payments \my\modules\superadmin\models\search\PaymentsSearch */
    /* @var $navs \my\modules\superadmin\models\search\PaymentsSearch */
    /* @var $filters */
    /* @var $status */
    /* @var $modes */
    /* @var $methods */
    /* @var $searchType array */

    use my\helpers\Url;
    use my\helpers\SpecialCharsHelper;
    use yii\helpers\Html;

    $this->context->addModule('superadminPaymentsController');
?>

    <ul class="nav nav-pills mb-3" role="tablist">
        <?php foreach ($navs as $code => $label) : ?>
            <?php $code = is_numeric($code) ? $code : null;?>
            <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute($code === null ? '/payments' : array_merge(['/payments'], $filters, ['status' => $code])) ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
        <li class="ml-auto">
            <form class="form" method="GET" id="paymentsSearch" action="<?=Url::toRoute(array_merge(['/payments'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'payments.list.search') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                    <?= Html::dropDownList('search-type', $searchType, $searchType, [
                        'class' => 'custom-select'
                    ]) ?>
                    <div class="input-group-append">
                        <button class="btn btn-light" type="submit"><span class="fa fa-search"></span></button>
                    </div>
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

<?= $this->render('layouts/_payment_details_modal')?>
<?= $this->render('layouts/_payment_refund_modal')?>