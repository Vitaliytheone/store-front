<?php
/* @var $this yii\web\View */
/* @var $stores \my\modules\superadmin\models\search\StoresSearch */

use my\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

?>
<table class="table table-border">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_id')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_domain')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_currency')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_language')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_customer')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_status')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'stores.list.column_created')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'stores.list.column_expiry')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_actions')?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($stores['models'])) : ?>
        <?php foreach ($stores['models'] as $store) : ?>
            <tr>
                <td>
                    <?= $store['id'] ?>
                </td>
                <td>
                    <?= $store['domain'] ?> <?= ($store['referrer_id'] ? '(' . Html::a('r', Url::toRoute(['/customers', 'id' => $store['referrer_id']]), ['target' => '_blank']) . ')' : '')?>
                </td>
                <td>
                    <?= $store['currency'] ?>
                </td>
                <td>
                    <?= $store['language'] ?>
                </td>
                <td>
                    <?= $store['customer_email'] ?>
                </td>
                <td>
                    <?= $store['status'] ?>
                </td>
                <td>
                    <span class="text-nowrap">
                        <?= $store['created_date'] ?>
                    </span>
                    <?= $store['created_time'] ?>
                </td>
                <td>
                    <span class="text-nowrap">
                        <?= $store['expired_date'] ?>
                    </span>
                    <?= $store['expired_date'] ?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'stores.list.column_actions')?></button>
                        <div class="dropdown-menu dropdown-menu-right">

                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    </tbody>
</table>

<div class="text-align-center pager">
    <?= LinkPager::widget([
        'pagination' => $stores['pages'],
    ]); ?>
</div>