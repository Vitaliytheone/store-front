<?php
/* @var $this yii\web\View */
/* @var $payments array */
/* @var $panel \common\models\panels\Project */

use my\helpers\Url;
use my\helpers\SpecialCharsHelper;
use yii\helpers\Html;

$this->context->addModule('superadminPanelsController');
?>
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
                            <div class="dropdown-menu dropdown-menu-right">
                                <?= Html::a(Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.delete'),
                                    Url::toRoute(['/panels/delete-payment-method', 'id' => $panel->id]), [
                                        'class' => 'dropdown-item',
                                        'data-method' => 'POST',
                                        'data-params' => ['method_id' => $payment['id']]
                                    ])?>
                                <?= Html::a(Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.allow'),
                                    Url::toRoute(['/panels/allow-payment', 'id' => $panel->id]), [
                                        'class' => 'dropdown-item allow-payment-method',
                                        'data-title' => Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.allow_confirm'),
                                        'data-method' => 'POST',
                                        'data-params' => ['method_id' => $payment['id'], 'allow' => 1]
                                    ])?>
                                <?= Html::a(Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.disallow'),
                                    Url::toRoute(['/panels/allow-payment', 'id' => $panel->id]), [
                                        'class' => 'dropdown-item disallow-payment-method',
                                        'data-title' => Yii::t('app/superadmin', 'panels.edit_payment_methods.dropdown.disallow_confirm'),
                                        'data-method' => 'POST',
                                        'data-params' => ['method_id' => $payment['id'], 'allow' => 0]
                                    ])?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

