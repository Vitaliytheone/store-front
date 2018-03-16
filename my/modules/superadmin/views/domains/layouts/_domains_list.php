<?php
/* @var $this yii\web\View */
/* @var $domains \my\modules\superadmin\models\search\DomainsSearch */
/* @var $domain \common\models\panels\Domains */

use my\helpers\Url;
use yii\helpers\Html;

?>
<table class="table table-border">
    <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'domains.list.column_id')?></th>
            <th><?= Yii::t('app/superadmin', 'domains.list.column_customer')?></th>
            <th><?= Yii::t('app/superadmin', 'domains.list.column_domain')?></th>
            <th class="text-nowrap"><?= Yii::t('app/superadmin', 'domains.list.column_created')?></th>
            <th><?= Yii::t('app/superadmin', 'domains.list.column_status')?></th>
            <th class="text-nowrap"><?= Yii::t('app/superadmin', 'domains.list.column_expiry')?></th>
            <th><?= Yii::t('app/superadmin', 'domains.list.column_privacy')?></th>
            <th><?= Yii::t('app/superadmin', 'domains.list.column_transfer')?></th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($domains['models'])) : ?>
            <?php foreach ($domains['models'] as $domain) : ?>
                <?php
                    $customer = $domain->customer;
                ?>
                <tr>
                    <td>
                        <?= $domain->id ?>
                    </td>
                    <td>
                        <a href="<?= Url::toRoute('/customers#' . $customer->id); ?>"><?= $customer->email ?></a>
                    </td>
                    <td>
                        <?= $domain->getDomain() ?>
                    </td>
                    <td>
                        <span class="text-nowrap">
                            <?= $domain->getFormattedDate('created_at', 'php:Y-m-d') ?>
                        </span>
                        <?= $domain->getFormattedDate('created_at', 'php:H:i:s') ?>
                    </td>
                    <td>
                        <?= $domain->getStatusName() ?>
                    </td>
                    <td>
                        <span class="text-nowrap">
                            <?= $domain->getFormattedDate('expiry', 'php:Y-m-d') ?>
                        </span>
                        <?= $domain->getFormattedDate('expiry', 'php:H:i:s') ?>
                    </td>
                    <td>
                        <?= ($domain->privacy_protection ? Yii::t('app/superadmin', 'domains.list.privacy_on') : Yii::t('app/superadmin', 'domains.list.privacy_off')) ?>
                    </td>
                    <td>
                        <?= ($domain->transfer_protection ? Yii::t('app/superadmin', 'domains.list.transfer_on') : Yii::t('app/superadmin', 'domains.list.transfer_off')) ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'domains.list.actions_label')?></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?= Html::a(Yii::t('app/superadmin', 'domains.list.action_details'), Url::toRoute(['/domains/details', 'id' => $domain->id]), [
                                    'class' => 'dropdown-item domain-details',
                                ])?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

    </tbody>
</table>