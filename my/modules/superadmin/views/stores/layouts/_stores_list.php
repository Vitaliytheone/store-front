<?php
/* @var $this yii\web\View */
/* @var $stores \my\modules\superadmin\models\search\StoresSearch */

use my\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use common\models\stores\Stores;

$now = time();

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
            <?php
                $loginUrl = Url::toRoute(['/stores/sign-in-as-admin', 'id' => $store['id']]);
            ?>
            <tr>
                <td>
                    <?= $store['id'] ?>
                </td>
                <td>
                    <div class="pull-left">
                        <?= $store['domain'] ?> <?= ($store['referrer_id'] ? '(' . Html::a('r', Url::toRoute(['/customers', 'id' => $store['referrer_id']]), ['target' => '_blank']) . ')' : '')?>
                    </div>
                    <div class="pull-right">
                        <a href="<?= $loginUrl ?>" class="login-key-link" target="_blank"><i class="fa fa-key fa-flip-horizontal" aria-hidden="true"></i></a>
                    </div>
                </td>
                <td>
                    <?= $store['currency'] ?>
                </td>
                <td>
                    <?= $store['language'] ?>
                </td>
                <td>
                    <?php if ($store['customer_id']) : ?>
                        <a href="<?= Url::toRoute(['/customers', 'id' => $store['customer_id']]); ?>" target="_blank"><?= $store['customer_email'] ?></a>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $store['status_name'] ?>
                </td>
                <td>
                    <span class="text-nowrap">
                        <?= $store['created_date'] ?>
                    </span>
                    <?= $store['created_time'] ?>
                </td>
                <td <?= ($now > $store['expired'] ? 'class="text-danger"' : '') ?>>
                    <span class="text-nowrap">
                        <?= $store['expired_date'] ?>
                    </span>
                    <?= $store['expired_time'] ?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'stores.list.column_actions')?></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?= Html::a(!empty($store['store_domain']) ? Yii::t('app/superadmin', 'stores.list.action_change_domain') : Yii::t('app/superadmin', 'stores.list.action_add_domain'), Url::toRoute(['/stores/change-domain', 'id' => $store['id']]), [
                                'class' => 'dropdown-item change-domain',
                                'data-domain' => $store['store_domain'],
                                'data-subdomain' => $store['subdomain'],
                                'data-title' => !empty($store['store_domain']) ? Yii::t('app/superadmin', 'stores.list.action_change_domain') : Yii::t('app/superadmin', 'stores.list.action_add_domain')
                            ])?>
                            <?php if (Stores::STATUS_ACTIVE == $store['status']) : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_freeze_store'), Url::toRoute(['/stores/change-status', 'id' => $store['id'], 'status' => Stores::STATUS_FROZEN]), ['class' => 'dropdown-item'])?>
                            <?php elseif (Stores::STATUS_FROZEN == $store['status']) : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_activate_store'), Url::toRoute(['/stores/change-status', 'id' => $store['id'], 'status' => Stores::STATUS_ACTIVE]), ['class' => 'dropdown-item'])?>
                            <?php endif; ?>
                            <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_edit_expiry'), Url::toRoute(['/stores/edit-expiry', 'id' => $store['id']]), [
                                'class' => 'dropdown-item edit-expiry',
                                'data-expired' => $store['expired_datetime']
                            ])?>
                            <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_sign_in_as_admin'), Url::toRoute(['/stores/sign-in-as-admin', 'id' => $store['id']]), ['class' => 'dropdown-item', 'target' => '_blank'])?>

                            <?php if(Stores::STATUS_FROZEN == $store['status']): ?>
                                <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_terminate'), Url::toRoute(['/stores/change-status', 'id' => $store['id'], 'status' => Stores::STATUS_TERMINATED]), ['class' => 'dropdown-item'])?>
                            <?php endif; ?>
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