<?php
/* @var $this yii\web\View */
/* @var $payments array */
/* @var $panel \common\models\panels\Project */
/* @var $model EditPanelPaymentMethodsForm */

use my\helpers\Url;
use my\helpers\SpecialCharsHelper;
use yii\helpers\Html;
use my\components\ActiveForm;
use superadmin\models\forms\EditPanelPaymentMethodsForm;
use common\models\panel\Users;

$this->context->addModule('superadminPanelsController');
?>
<ul class="nav nav-pills mb-3" role="tablist">
    <li>
        <?php $form = ActiveForm::begin([
            'id' => 'editPaymentMethodsForm',
            'options' => [
                'class' => "form",
                'action' => Url::toRoute(['panels/edit-payment-methods', 'id' => $panel->id])
            ],
            'fieldClass' => 'yii\bootstrap\ActiveField',
            'fieldConfig' => [
                'template' => "{label}\n{input}",
            ],
        ]); ?>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <?= Html::activeDropDownList($model, 'currency_id', $model->getPaymentMethodDropdown(), [
                        'prompt' => Yii::t('app/superadmin', 'panels.edit.payment_methods.select_payment_method'),
                        'class' => 'form-control',
                        'style' => 'max-width: 200px;'
                    ]) ?>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app/superadmin', 'panels.edit.payment_methods.add_method'), [
                        'class' => 'btn btn-light',
                        'name' => 'edit-expiry-button',
                        'id' => 'addPaymentMethodBtn'
                    ]) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </li>
</ul>

<div class="tab-pane fade show active" id="status-all" role="tabpanel">
    <table class="table table-sm table-custom">
        <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'panels.edit_payment_methods.header_name')?></th>
            <th><?= Yii::t('app/superadmin', 'panels.edit_payment_methods.header_currency')?></th>
            <th class="table-custom__action-th"></th>
        </tr>
        </thead>
        <tbody>
            <?php foreach (SpecialCharsHelper::multiPurifier($payments) as $payment) : ?>
                <tr>
                    <td><?= $payment['method_name'] ?></td>
                    <td><?= $payment['currency'] ?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= Yii::t('app/superadmin', 'panels.list.actions')?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right multi-level">
                                <?= Html::a(Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.delete'),
                                    Url::toRoute(['/panels/delete-payment-method', 'id' => $panel->id, 'method_id' => $payment['id']]), [
                                        'class' => 'dropdown-item',
                                    ])?>
                                <?= Html::a(Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.allow'),
                                    Url::toRoute(['/panels/allow-payment', 'id' => $panel->id, 'method_id' => $payment['id'], 'allow' => Users::PAYMENT_METHOD_ALLOW]), [
                                        'class' => 'dropdown-item allow-payment-method',
                                        'data-title' => Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.allow_confirm'),
                                    ])?>
                                <?= Html::a(Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.disallow'),
                                    Url::toRoute(['/panels/allow-payment', 'id' => $panel->id, 'method_id' => $payment['id'], 'allow' => Users::PAYMENT_METHOD_DISALLOW]), [
                                        'class' => 'dropdown-item disallow-payment-method',
                                        'data-title' => Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.disallow_confirm'),
                                    ])?>
                                <div class="dropdown-submenu">
                                    <?= Html::a(Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.same_as'), '', ['class' => 'dropdown-item']) ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach (SpecialCharsHelper::multiPurifier($payments) as $sameMethod): ?>
                                        <?php if ($payment['id'] == $sameMethod['id']) {
                                            continue;
                                            } ?>
                                        <li>
                                        <?= Html::a($sameMethod['method_name'], Url::toRoute([
                                            '/panels/allow-payment-with-same',
                                            'id' => $panel->id,
                                            'method_id' => $payment['id'],
                                            'same_method_id' => $sameMethod['id'],
                                        ])) ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

