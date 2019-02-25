<?php
    /* @var $this yii\web\View */
    /* @var $customer \common\models\panels\Customers */

    use common\models\panels\ReferralVisits;
    use control_panel\helpers\SpecialCharsHelper;

    $referralVisits = $customer->referralVisits;

?>

<table class="table table-sm table-custom">
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
        <?php foreach (SpecialCharsHelper::multiPurifier($referralVisits) as $referralVisit) : ?>
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
