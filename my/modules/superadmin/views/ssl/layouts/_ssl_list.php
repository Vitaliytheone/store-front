<?php
/* @var $this yii\web\View */
/* @var $sslList \my\modules\superadmin\models\search\SslSearch */
/* @var $ssl \my\modules\superadmin\models\search\SslSearch */

use my\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>
<table class="table table-border">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'ssl.list.column_id')?></th>
        <th><?= Yii::t('app/superadmin', 'ssl.list.column_customer')?></th>
        <th><?= Yii::t('app/superadmin', 'ssl.list.column_panel')?></th>
        <th><?= Yii::t('app/superadmin', 'ssl.list.column_status')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'ssl.list.column_created')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'ssl.list.column_expiry')?></th>
        <th class="w-1"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($sslList['models'])) : ?>
        <?php foreach ($sslList['models'] as $ssl) : ?>
            <tr>
                <td>
                    <?= $ssl->id ?>
                </td>
                <td>
                    <a href="<?= Url::toRoute('/customers#' . $ssl->cid); ?>"><?= $ssl->email ?></a>
                </td>
                <td>
                    <?= $ssl->getDomain() ?>
                </td>
                <td>
                    <?= $ssl->getStatusName() ?>
                </td>
                <td>
                    <span class="text-nowrap">
                        <?= $ssl->getFormattedDate('created_at', 'php:Y-m-d') ?>
                    </span>
                    <?= $ssl->getFormattedDate('created_at', 'php:H:i:s') ?>
                </td>
                <td class="text-nowrap">
                    <?= $ssl->expiry ?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'ssl.list.actions_label')?></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?= Html::a(Yii::t('app/superadmin', 'ssl.list.action_details'), Url::toRoute(['/ssl/details', 'id' => $ssl->id]), [
                                'class' => 'dropdown-item ssl-details',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    </tbody>
</table>