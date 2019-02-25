<?php

/* @var $this yii\web\View */
/* @var $reports array */

use control_panel\helpers\SpecialCharsHelper;
use yii\helpers\Html;
use control_panel\helpers\Url;
use common\models\panels\PaypalFraudReports;

?>

<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'fraud_reports.list.id') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_reports.list.panel') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_reports.list.user') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_reports.list.payment_id') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_reports.list.report') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_reports.list.status') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_reports.list.created') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_reports.list.updated') ?></th>
        <th class="table-custom__action-th"></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (SpecialCharsHelper::multiPurifier($reports) as $report) : ?>
        <tr>
            <td>
                <?= $report['id'] ?>
            </td>
            <td>
                <?= Html::a($report['panel'], Url::toRoute([$report['child_panel'] ? '/child-panels' : '/panels', 'id' => $report['panel_id']]), [
                        'target' => '_blank',
                ]); ?>
            </td>
            <td>
                <?= $report['user_id'] ?>
            </td>
            <td>
                <?= $report['payment_id'] ?>
            </td>
            <td>
                <?= $report['report'] ?>
            </td>
            <td>
                <?= PaypalFraudReports::getStatusName($report['status']) ?>
            </td>
            <td>
                <?= $report['created_at'] ?>
            </td>
            <td>
                <?= $report['updated_at'] ?>
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><?= Yii::t('app/superadmin', 'customers.dropdown.actions_label') ?></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?= Html::a(Yii::t('app/superadmin', 'payments.list.action_details'), Url::toRoute(['/fraud/report-details', 'id' => $report['id']]), [
                            'class' => 'dropdown-item report-details',
                        ])?>
                        <h6 class="dropdown-header"><?= Yii::t('app/superadmin', 'fraud_reports.dropdown.header') ?></h6>
                        <?php if ($report['status'] == PaypalFraudReports::STATUS_PENDING) : ?>
                            <?= Html::a(Yii::t('app/superadmin', 'fraud_reports.dropdown.accept'),
                                Url::toRoute('/fraud/reports-change-status'),
                                ['class' => 'dropdown-item', 'data-method' => 'POST', 'data-params' => ['id' => $report['id'], 'status' => PaypalFraudReports::STATUS_ACCEPTED]]
                            )?>
                            <?= Html::a(Yii::t('app/superadmin', 'fraud_reports.dropdown.reject'),
                                Url::toRoute('/fraud/reports-change-status'),
                                ['class' => 'dropdown-item', 'data-method' => 'POST', 'data-params' => ['id' => $report['id'], 'status' => PaypalFraudReports::STATUS_REJECTED]]
                            )?>
                        <?php elseif ($report['status'] == PaypalFraudReports::STATUS_REJECTED) : ?>
                            <?= Html::a(Yii::t('app/superadmin', 'fraud_reports.dropdown.accept'),
                                Url::toRoute('/fraud/reports-change-status'),
                                ['class' => 'dropdown-item', 'data-method' => 'POST', 'data-params' => ['id' => $report['id'], 'status' => PaypalFraudReports::STATUS_ACCEPTED]]
                            )?>
                        <?php elseif ($report['status'] == PaypalFraudReports::STATUS_ACCEPTED) : ?>
                            <?= Html::a(Yii::t('app/superadmin', 'fraud_reports.dropdown.reject'),
                                Url::toRoute('/fraud/reports-change-status'),
                                ['class' => 'dropdown-item', 'data-method' => 'POST', 'data-params' => ['id' => $report['id'], 'status' => PaypalFraudReports::STATUS_REJECTED]]
                            )?>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>



