<?php
    /* @var $this yii\web\View */
    /* @var $referrals \superadmin\models\search\ReferralsPaymentsSearch[] */

    use my\helpers\SpecialCharsHelper;

?>
<div class="container-fluid mt-3">
    <table class="table table-border">
        <thead>
            <tr>
                <th><?= Yii::t('app/superadmin', 'referrals.list.referrals_customer_id')?></th>
                <th><?= Yii::t('app/superadmin', 'referrals.list.referrals_customer_email')?></th>
                <th><?= Yii::t('app/superadmin', 'referrals.list.referrals_customer_date')?></th>
                <th><?= Yii::t('app/superadmin', 'referrals.list.referrals_customer_paid')?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($referrals['models'])) : ?>
                <?php foreach (SpecialCharsHelper::multiPurifier($referrals['models']) as $referral) : ?>
                    <tr>
                        <td>
                            <?= $referral['id'] ?>
                        </td>
                        <td>
                            <?= $referral['email'] ?>
                        </td>
                        <td>
                            <?= $referral['date_create'] ?>
                        </td>
                        <td>
                            <?= $referral['paid'] ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>