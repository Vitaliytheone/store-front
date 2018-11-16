<?php

use yii\helpers\Html;
use my\helpers\Url;
use superadmin\models\forms\EditPanelPaymentMethodsForm;

/* @var $this yii\web\View */
/* @var $payments array */
/* @var $model EditPanelPaymentMethodsForm */

?>
<table class="table">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'panels.edit.payment_methods.field.name'); ?></th>
        <th><?= Yii::t('app/superadmin', 'panels.edit.payment_methods.field.active'); ?></th>
    </tr>
    </thead>
    <tbody id="sortable">
    <?php foreach ($payments as $payment) : ?>
        <tr>
            <td><?= $payment['method_name'] ?></td>
            <td>
                <?= Html::checkbox($model->formName() . '[methods][' . $payment['id'] . ']', $payment['active'])?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>