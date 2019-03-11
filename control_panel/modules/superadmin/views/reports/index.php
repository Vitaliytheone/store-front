<?php

use yii\helpers\ArrayHelper;
use control_panel\helpers\Url;

/* @var $this yii\web\View */
/* @var $reportData array */
/* @var $filters array */
/* @var $years array */
/* @var $paymentParams array */

$currentParamsCode = array_search(true, array_column($paymentParams, 'active'));
$currentParamsName = ArrayHelper::getValue($paymentParams, "$currentParamsCode.name");

error_log(print_r($reportData,1));
?>

    <div class="row">
        <div class="col-md-6">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-payments" href="#" role="tab" aria-controls="pills-home" aria-selected="true"><?= Yii::t('app/superadmin', 'reports.list.button.payments') ?></a>
                </li>
            </ul>
        </div>
        <div class="col-md-6 text-md-right">
            <div class="d-flex justify-content-md-end">
                <div class="btn-group mr-3 mb-3" role="group" aria-label="Basic example">
                    <?php foreach ($years as $year): ?>
                        <a href="<?= $year['active'] ? '#' : Url::toRoute(array_merge($filters, ['/reports/payments', 'year' => $year['year']])) ?>" class="btn btn-light <?= $year['active'] ? 'active' : '' ?>"><?= $year['year'] ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $currentParamsName ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <?php foreach ($paymentParams as $param): ?>
                            <a class="dropdown-item <?= $param['active'] ? 'active' : '' ?>" href="<?= $param['active'] ? '#' : Url::toRoute(array_merge($filters, ['/reports/payments', 'params' => $param['code']])) ?>"><?= $param['name'] ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-payments">
                    <table class="table table-sm table-custom">
                        <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.january') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.february') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.march') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.april') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.may') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.june') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.july') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.august') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.september') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.october') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.november') ?></th>
                            <th><?= Yii::t('app/superadmin', 'reports.list.months.december') ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php for ($day = 1; $day <= 31; $day++): ?>
                        <tr>
                                <td><?= $day ?></td>
                            <?php for ($month = 1; $month <= 12; $month++): ?>
                                <td>
                                    <?= number_format($reportData[$month][$day]['amount'], 2, ',', ''); ?> (<?= $reportData[$month][$day]['count'] ?>)
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endfor; ?>

                        <tr>
                            <td>
                                <b><?= Yii::t('app/superadmin', 'reports.list.total') ?></b>
                            </td>
                            <?php for ($month = 1; $month <= 12; $month++): ?>
                                <td>
                                    <b><?= number_format($reportData[$month]['month_total']['amount'], 2, ',', '') ?></b> (<?= $reportData[$month]['month_total']['count'] ?>)
                                </td>
                            <?php endfor; ?>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>