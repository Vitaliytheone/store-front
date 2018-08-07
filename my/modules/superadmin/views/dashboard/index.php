<?php
/* @var $this \yii\web\View */
/* @var $content string */
/* @var $activePanel string */
/* @var $dashboardBlocks array */
/* @var $dashboardServices array */

use yii\helpers\Html;
use \my\modules\superadmin\helpers\DashboardBlocks;
use my\helpers\Url;
use my\helpers\SpecialCharsHelper;

$this->context->addModule('superadminDashboardController', [
    'error' => Yii::t('app/superadmin', 'error'),
    'titles' => DashboardBlocks::getLabels()
]);
?>

<div class="row">
    <div class="col-md-8">
        <div class="row dashboard2-card__row align-items-stretch">
            <?php foreach ($dashboardBlocks as $key => $panel) : ?>
                <?php $active = $key == $activePanel ? 'dashboard2-card__active' : ''; ?>
                <div class="col <?= $active ?>" data-panel = '<?= $key ?>' data-action="<?= Url::to(['/dashboard/block', 'name' => $key]) ?>">
                    <div class="dashboard2-card">
                        <div class="dashboard2-card__value">
                            <?= $panel->getCount() ?>
                        </div>
                        <div class="dashboard2-card__title"><?= $panel->getName() ?></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="col" data-panel = "hosted-email">
                <div class="dashboard2-card">
                    <div class="dashboard2-card__value">
                        0
                    </div>
                    <div class="dashboard2-card__title"><?= Yii::t('app/superadmin', 'dashboard.hosted_email') ?></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="dashboard2-tab__content">
                    <div class="table-responsive">
                        <table class="table table-mobile">
                            <thead>
                                <tr>
                                    <th data-title="<?= Yii::t('app/superadmin', 'dashboard.table.id') ?>">
                                        <?= Yii::t('app/superadmin', 'dashboard.table.id') ?>
                                    </th>
                                    <th data-title="<?= Yii::t('app/superadmin', 'dashboard.table.domain') ?>">
                                        <?= Yii::t('app/superadmin', 'dashboard.table.domain') ?>
                                    </th>
                                    <th data-title="<?= Yii::t('app/superadmin', 'dashboard.table.customer') ?>">
                                        <?= Yii::t('app/superadmin', 'dashboard.table.customer') ?>
                                    </th>
                                    <th data-title="<?= Yii::t('app/superadmin', 'dashboard.table.status') ?>">
                                        <?= Yii::t('app/superadmin', 'dashboard.table.status') ?>
                                    </th>
                                    <th data-title="<?= Yii::t('app/superadmin', 'dashboard.table.created') ?>">
                                        <?= Yii::t('app/superadmin', 'dashboard.table.created') ?>
                                    </th>
                                    <th data-title="<?= Yii::t('app/superadmin', 'dashboard.table.expiry') ?>">
                                        <?= Yii::t('app/superadmin', 'dashboard.table.expiry') ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboardBlocks[$activePanel]->getEntities() as $row) : ?>
                                   <tr>
                                       <td data-title="<?= Yii::t('app/superadmin', 'dashboard.table.id') ?>">
                                           <?= $row['id'] ?>
                                       </td>
                                       <td data-title="<?= Yii::t('app/superadmin', 'dashboard.table.domain') ?>">
                                           <?= $row['domain'] ?>
                                       </td>
                                       <td data-title="<?= Yii::t('app/superadmin', 'dashboard.table.customer') ?>">
                                           <?= Html::tag('a', $row['customer'], ['href' => '#']); ?>
                                       </td>
                                       <td data-title="<?= Yii::t('app/superadmin', 'dashboard.table.status') ?>">
                                           <?= $row['status'] ?>
                                       </td>
                                       <td data-title="<?= Yii::t('app/superadmin', 'dashboard.table.created') ?>">
                                           <?= $row['created'] ?>
                                       </td>
                                       <td data-title="<?= Yii::t('app/superadmin', 'dashboard.table.expiry') ?>">
                                           <?= $row['expired'] ?>
                                       </td>
                                   </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <h1 id="loader" class="text-center" style="display: none"class="text-center"><i class="fa fa-spinner fa-spin" style="font-size: 24px;"></i></h1>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-4">
        <div class="m-portlet__body m-portlet__body-balances">
            <?php foreach ($dashboardServices as $key => $service) : ?>
                <div class="balances-line">
                    <div class="balances-line__title" ><?= $service->getName() ?></div>
                    <div class="balances-line__value dashboardService" data-service="<?= $key ?>"  data-action="<?= Url::to(['/dashboard/balance', 'serviceName' => $key]) ?>">
                        <i class="fa fa-spinner fa-spin" style="font-size:24px;"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>