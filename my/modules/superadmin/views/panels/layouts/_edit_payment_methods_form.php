<?php

use yii\helpers\Html;
use superadmin\models\forms\EditPanelPaymentMethodsForm;

/* @var $this yii\web\View */
/* @var $payments array */
/* @var $model EditPanelPaymentMethodsForm */


?>

<div class="form-group">
    <div class="input-group">
        <?= Html::activeDropDownList($model, 'currency_id', $model->getPaymentMethodDropdown(), [
            'prompt' => Yii::t('app/superadmin', 'panels.edit.payment_methods.select_payment_method'),
            'class' => 'form-control',
        ]) ?>
        <div class="input-group-append">
            <?= Html::submitButton(Yii::t('app/superadmin', 'panels.edit.payment_methods.add_method'), [
                'class' => 'btn btn-light',
                'name' => 'edit-expiry-button',
                'id' => 'addPaymentMethodBtn'
            ]) ?>
        </div>
    </div>
</div>

<table class="table">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'panels.edit.payment_methods.field.name'); ?></th>
        <th><?= Yii::t('app/superadmin', 'panels.edit.payment_methods.field.currency'); ?></th>
        <th><?= Yii::t('app/superadmin', 'panels.edit.payment_methods.field.active'); ?></th>
    </tr>
    </thead>
    <tbody id="sortable">
    <?php foreach ($payments as $payment) : ?>
        <tr>
            <td><?= $payment['method_name'] ?></td>
            <td><?= $payment['currency'] ?></td>
            <td>
                <?= Html::checkbox($model->formName() . '[methods][' . $payment['currency_id'] . ']', 1)?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>