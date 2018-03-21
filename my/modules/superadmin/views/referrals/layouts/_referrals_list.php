<?php
    /* @var $this yii\web\View */
    /* @var $referrals \my\modules\superadmin\models\search\PanelsSearch */

    use my\helpers\Url;
    use yii\bootstrap\Html;
?>
<table class="table table-border tablesorter-bootstrap" id="referralsTable">
    <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'referrals.list.customer_id')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.customer_email')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.total_visits')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.unpaid_referrals')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.paid_referrals')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.conversion_rate')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.total_earnings')?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.unpaid_earnings')?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($referrals['models'])) : ?>
            <?php foreach ($referrals['models'] as $referral) : ?>
                <tr>
                    <td>
                        <?= $referral['id'] ?>
                    </td>
                    <td>
                        <?= $referral['email'] ?>
                    </td>
                    <td>
                        <?php if ($referral['total_visits']) : ?>
                            <?= Html::a($referral['total_visits'], Url::toRoute(['/referrals/total-visits', 'id' => $referral['id']])) ?>
                        <?php else : ?>
                            <?= $referral['total_visits'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($referral['unpaid_referrals']) : ?>
                            <?= Html::a($referral['unpaid_referrals'], Url::toRoute(['/referrals/unpaid-referrals', 'id' => $referral['id']])) ?>
                        <?php else : ?>
                            <?= $referral['unpaid_referrals'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($referral['paid_referrals']) : ?>
                            <?= Html::a($referral['paid_referrals'], Url::toRoute(['/referrals/paid-referrals', 'id' => $referral['id']])) ?>
                        <?php else : ?>
                            <?= $referral['paid_referrals'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $referral['conversion_rate'] ?>
                    </td>
                    <td>
                        <?php if ($referral['total_earnings']) : ?>
                            <?= Html::a($referral['total_earnings'], Url::toRoute(['/referrals/total-earnings', 'id' => $referral['id']])) ?>
                        <?php else : ?>
                            <?= $referral['total_earnings'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $referral['unpaid_earnings'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>