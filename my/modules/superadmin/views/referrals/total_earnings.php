<?php
    /* @var $this yii\web\View */
    /* @var $referralEarnings \superadmin\models\search\ReferralEarningsSearch */

    use yii\bootstrap\Html;
    use my\helpers\Url;
    use my\helpers\SpecialCharsHelper;

?>

<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'referrals.list.total_earnings_id')?></th>
        <th><?= Yii::t('app/superadmin', 'referrals.list.total_earnings_value')?></th>
        <th><?= Yii::t('app/superadmin', 'referrals.list.total_earnings_site')?></th>
        <th><?= Yii::t('app/superadmin', 'referrals.list.total_earnings_invoice_id')?></th>
        <th><?= Yii::t('app/superadmin', 'referrals.list.total_earnings_status')?></th>
        <th><?= Yii::t('app/superadmin', 'referrals.list.total_earnings_date')?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($referralEarnings['models'])) : ?>
        <?php foreach (SpecialCharsHelper::multiPurifier($referralEarnings['models']) as $referralEarning) : ?>
            <tr>
                <td>
                    <?= $referralEarning['id'] ?>
                </td>
                <td>
                    <?= $referralEarning['earnings'] ?>
                </td>
                <td>
                    <?= $referralEarning['site'] ?>
                </td>
                <td>
                    <?= Html::a($referralEarning['invoice_id'], Url::to(['/invoices', 'id' => $referralEarning['invoice_id']])) ?>
                </td>
                <td>
                    <?= $referralEarning['status'] ?>
                </td>
                <td>
                    <?= $referralEarning['created_at'] ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>