<?php
/* @var $this yii\web\View */
/* @var $filters array */
/* @var $payments array */
/* @var $searchTypes array */

use my\helpers\Url;
use my\helpers\SpecialCharsHelper;
use yii\helpers\Html;

?>
<ul class="nav nav-pills mb-3">
    <li class="ml-auto">
        <form class="form" method="GET" id="paymentsSearch" action="<?=Url::toRoute(array_merge(['/fraud/payments'], $filters, ['query' => null]))?>">
            <div class="input-group">
                <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'fraud_payments.list.search') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">

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

<?= $this->render('layouts/payments/_payments_list', [
    'payments' => $payments,
    'filters' => $filters,
])?>

