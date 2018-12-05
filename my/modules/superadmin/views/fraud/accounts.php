<?php

/* @var $this yii\web\View */
/* @var $accounts array */
/* @var $filters array */
/* @var $searchTypes array */

use my\helpers\SpecialCharsHelper;
use yii\widgets\LinkPager;
use superadmin\widgets\CountPagination;
use yii\helpers\Html;
use my\helpers\Url;

?>
<ul class="nav nav-pills mb-3">
    <li class="ml-auto">
        <form class="form" method="GET" id="paymentsSearch" action="<?=Url::toRoute(array_merge(['/fraud/accounts'], $filters, ['query' => null]))?>">
            <div class="input-group">
                <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'fraud_accounts.list.search') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">

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
<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.id') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.payer_id') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.payer_email') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.lastname') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.firstname') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.risk') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.status') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.created') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud.accounts.list.updated') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (SpecialCharsHelper::multiPurifier($accounts['models']) as $account) : ?>
        <tr>
            <td>
                <?= $account['id'] ?>
            </td>
            <td>
                <?= Html::a($account['payer_id'],
                    Url::toRoute(['/fraud/payments', 'query' => $account['payer_id'],'search_type' => 'payer_id']))?>
            </td>
            <td>
                <?= $account['payer_email'] ?>
            </td>
            <td>
                <?= $account['lastname'] ?>
            </td>
            <td>
                <?= $account['firstname'] ?>
            </td>
            <td>
                <?= $account['fraud_risk'] ?>
            </td>
            <td>
                <?= $account['payer_status'] ?>
            </td>
            <td>
                <?= $account['created_at'] ?>
            </td>
            <td>
                <?= $account['updated_at'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="row">
    <div class="col-md-6">
        <nav>
            <ul class="pagination">
                <?= LinkPager::widget(['pagination' => $accounts['pages'],]); ?>
            </ul>
        </nav>
    </div>
    <div class="col-md-6 text-md-right">
        <?= CountPagination::widget([
            'pages' => $accounts['pages'],
            'params' => $filters
        ]) ?>
    </div>
</div>
