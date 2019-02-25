<?php
    /* @var $this yii\web\View */
    /* @var $customer \common\models\panels\Customers */
    /* @var $referral array */
    /* @var $note string */

    use yii\helpers\Url;
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"><?= Yii::t('app', 'referral.index.header')?></h2>
    </div>
</div>
<div class="row">
    <div class="col-md-8">

        <div class="manual-panel">
            <?= (string)$note ?>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="referral-link__block">
            <?= Yii::t('app', 'referral.index.url_comment', [
                'url' => '<strong>https://perfectpanel.com/ref/' . $customer->referral_link . '</strong>'
            ])?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th><?= Yii::t('app', 'referral.index.column_total_visits')?></th>
                <th><?= Yii::t('app', 'referral.index.column_unpaid_referrals')?></th>
                <th><?= Yii::t('app', 'referral.index.column_paid_referrals')?></th>
                <th><?= Yii::t('app', 'referral.index.column_conversion_rate')?></th>
                <th><?= Yii::t('app', 'referral.index.column_total_earnings')?></th>
                <th><?= Yii::t('app', 'referral.index.column_unpaid_earnings')?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?= $referral['total_visits'] ?></td>
                <td><?= $referral['unpaid_referrals'] ?></td>
                <td><?= $referral['paid_referrals'] ?></td>
                <td><?= (is_float($referral['conversion_rate']) ? number_format($referral['conversion_rate'], 2) : $referral['conversion_rate']) ?>%</td>
                <td>$<?= number_format($referral['total_earnings'], 2) ?></td>
                <td>$<?= number_format($referral['unpaid_earnings'], 2) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>