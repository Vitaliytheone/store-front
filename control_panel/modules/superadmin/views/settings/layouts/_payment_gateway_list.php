<?php
    /* @var $this yii\web\View */
    /* @var $payments \superadmin\models\search\PaymentMethodsSearch */
    /* @var $payment \common\models\sommerces\Params */

    use control_panel\helpers\Url;
    use common\models\sommerces\Params;
    use yii\bootstrap\Html;
?>
<table class="table table-sm table-custom">
    <thead>
        <tr>
            <th scope="col"><?= Yii::t('app/superadmin', 'payments.list.method') ?></th>
            <th scope="col"><?= Yii::t('app/superadmin', 'payments.list.visibility') ?></th>
            <th class="table-custom__action-th"></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($payments) : ?>
            <?php foreach ($payments as $payment) : ?>
                <tr <?= (Params::VISIBILITY_DISABLED == $payment['visibility'] ? 'class="text-muted"' : '') ?>>
                    <td><?= $payment['name'] ?></td>
                    <td><?= $payment['visibility_string'] ?></td>
                    <td class="text-right">
                        <?= Html::a(Yii::t('app/superadmin', 'payments.list.dropdown_edit'), Url::toRoute(['/settings/edit-payment',
                            'code' => $payment['code']
                        ]), [
                            'class' => 'btn btn-primary btn-sm edit-payment'
                        ])?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="3"><?= Yii::t('app/superadmin', 'payments.list.no_payments') ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>