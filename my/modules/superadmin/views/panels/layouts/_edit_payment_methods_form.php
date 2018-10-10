<?php

use yii\helpers\Html;
use my\helpers\Url;

/* @var $this yii\web\View */
/* @var $payments array */

?>
<table class="table">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'payments.index.list.field.name'); ?></th>
        <th><?= Yii::t('app/superadmin', 'payments.index.list.field.min'); ?></th>
        <th><?= Yii::t('app/superadmin', 'payments.index.list.field.max'); ?></th>
        <th><?= Yii::t('app/superadmin', 'payments.index.list.field.new_users'); ?></th>
        <th><?= Yii::t('app/superadmin', 'payments.index.list.field.visibility'); ?></th>
    </tr>
    </thead>
    <tbody id="sortable">
    <?php foreach ($payments['models'] as $payment) : ?>
        <tr<?= ($payment['visibility'] ? '' : ' class="grey"') ?>>
            <td><?= $payment['name'] ?></td>
            <td><?= $payment['min'] ?></td>
            <td><?= $payment['max'] ?></td>
            <td><?= ($payment['new_users'] ? 'Allowed' : 'Not allowed') ?></td>
            <td>
                <div class="switch-custom switch-custom__table">
                    <label class="switch">
                        <?= Html::checkbox('PaymentMethod[' . $payment['id'] . '][visibility]', $payment['visibility'], [
                            'class' => 'toggle-payment-method',
                            'data' => [
                                'action' => Url::toRoute(['panels/toggle-payment-method', 'id' => $payment['id']])
                            ]
                        ])?>
                        <span class="slider round"></span>
                    </label>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>