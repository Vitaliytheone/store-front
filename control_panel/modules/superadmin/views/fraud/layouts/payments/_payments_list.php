<?php
/* @var $this yii\web\View */
/* @var $payments array */
/* @var $panel Project */

use control_panel\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use common\models\panels\Project;
use control_panel\helpers\SpecialCharsHelper;
use superadmin\widgets\CountPagination;

?>
<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.id')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.panel')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.payment_id')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.payer_id')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.email')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.status')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.firstname')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.lastname')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.created')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.updated')?></th>
        <th class="table-custom__action-th"></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach (SpecialCharsHelper::multiPurifier($payments['models']) as $key => $payment) : ?>
            <tr>
                <td>
                    <?= $payment['id'] ?>
                </td>
                <td>
                    <?php $panel = $payment['panel']; ?>
                    <?= Html::a($panel->site, Url::toRoute([$panel->child_panel === 0 ? '/panels' : '/child-panels', 'id' => $panel->id]))?>
                </td>
                <td class="table-custom__customer-td">
                    <?= $payment['payment_id'] ?>
                    <a href="<?= Url::toRoute(['/panels/sign-in-as-admin', 'id' => $panel->id, 'redirect' => '/admin/payments?query=' . $payment['payment_id'] . '&search_type=1']); ?>" target="_blank" class="table-custom__customer-button"  data-placement="top" title="">
                        <span class="my-icons my-icons-autorization"></span>
                    </a>
                </td>
                <td>
                    <?= $payment['payer_id'] ?>
                </td>
                <td>
                    <?= $payment['payer_email'] ?>
                </td>
                <td>
                    <?= $payment['paypal_status'] ?>
                </td>
                <td>
                    <?= $payment['firstname'] ?>
                </td>
                <td>
                    <?= $payment['lastname'] ?>
                </td>
                <td>
                    <?= $payment['created_at'] ?>
                </td>
                <td>
                    <?= $payment['updated_at'] ?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><?= Yii::t('app/superadmin', 'customers.dropdown.actions_label') ?></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?= Html::a(Yii::t('app/superadmin', 'fraud_payments.action.details'),
                                Url::toRoute(['/fraud/payment-details', 'id' => $payment['id']]),
                                ['class' => 'dropdown-item payment-details']
                            )?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>

    </tbody>
</table>

<div class="row">
    <div class="col-md-6">
        <nav>
            <ul class="pagination">
                <?= LinkPager::widget(['pagination' => $payments['pages'],]); ?>
            </ul>
        </nav>
    </div>
    <div class="col-md-6 text-md-right">
        <?= CountPagination::widget([
            'pages' => $payments['pages'],
            'params' => $filters
        ]) ?>
    </div>
</div>