<?php
    /* @var $this yii\web\View */
    /* @var $customer \common\models\panels\Customers */

    use common\models\panels\ReferralVisits;

    $referralVisits = $customer->referralVisits;

?>
<div class="container-fluid mt-3">
    <table class="table table-border">
        <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'referrals.list.total_visit_ip')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.total_visit_user_agent')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.total_visit_http_referer')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.total_visit_date')?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($referralVisits)) : ?>
            <?php foreach ($referralVisits as $referralVisit) : ?>
                <tr>
                    <td>
                        <?= $referralVisit->ip ?>
                    </td>
                    <td class="break-all-words">
                        <?= $referralVisit->user_agent ?>
                    </td>
                    <td class="break-all-words">
                        <?= $referralVisit->http_referer ?>
                    </td>
                    <td>
                        <?= ReferralVisits::formatDate($referralVisit->created_at) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
