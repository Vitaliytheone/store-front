<?php
    /* @var $this yii\web\View */
    /* @var $payments \my\modules\superadmin\models\search\PaymentGatewaySearch */
    /* @var $payment \common\models\panels\PaymentGateway */

    use my\helpers\Url;
    use common\models\panels\PaymentGateway;
    use yii\bootstrap\Html;
?>
<table class="table mb-0">
    <thead>
        <tr>
            <th class="border-0">Method</th>
            <th class="border-0">Visibility</th>
            <th class="border-0"></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($payments) : ?>
            <?php foreach ($payments as $payment) : ?>
                <tr <?= (PaymentGateway::VISIBILITY_DISABLED == $payment->visibility ? 'class="text-muted"' : '') ?>>
                    <td><?= $payment->name ?></td>
                    <td><?= $payment->getVisibilityName() ?></td>
                    <td class="text-right">
                        <?= Html::a('Edit', Url::toRoute(['/settings/edit-payment', 'id' => $payment->id]), [
                            'class' => 'btn btn-secondary btn-sm edit-payment'
                        ])?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="3">No payments</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>