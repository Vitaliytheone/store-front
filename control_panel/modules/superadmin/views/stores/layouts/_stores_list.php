<?php
/* @var $this yii\web\View */
/* @var $stores \superadmin\models\search\StoresSearch */
/* @var $filters array */

use control_panel\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use common\models\stores\Stores;
use yii\helpers\Json;
use superadmin\widgets\CountPagination;

$now = time();

?>
<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_id')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_domain')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_currency')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_language')?></th>
        <th class="table-custom__customer-th"><?= Yii::t('app/superadmin', 'stores.list.column_customer')?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_orders') ?></th>
        <th><?= Yii::t('app/superadmin', 'stores.list.column_status')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'stores.list.column_created')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'stores.list.column_expiry')?></th>
        <th class="table-custom__action-th"></th>
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
                <td class="table-custom__customer-td">
                    <?php $referralView = Html::a(
                        Html::tag(
                            'span',
                            '',
                            [
                                'class' => 'my-icons my-icons-referral',
                                'data-placement' => 'top',
                            ]
                        ), Url::toRoute(['/customers', 'query' => $store['customer_email']]), ['target' => '_blank']
                    );  ?>
                    <?= $store['domain'] ?> <?= ($store['referrer_id'] ? ' ' . $referralView : '')?>
                    <a href="<?= $loginUrl ?>" class="table-custom__customer-button" data-placement="top" target="_blank">
                        <span class="my-icons my-icons-autorization"></span>
                    </a>
                </td>
                <td>
                    <?= $store['currency'] ?>
                </td>
                <td>
                    <?= $store['language'] ?>
                </td>
                <td class="table-custom__customer-td">
                    <?php if ($store['customer_id']) : ?>
                        <a href="<?= Url::toRoute(['/customers', 'query' => $store['customer_email']]); ?>" target="_blank"><?= $store['customer_email'] ?></a>
                    <?php endif; ?>
                </td>
                <td><?= $store['last_count'] . ' / ' . $store['current_count'] ?></td>
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
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><?= Yii::t('app/superadmin', 'stores.list.column_actions')?></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?= Html::a(!empty($store['store_domain']) ? Yii::t('app/superadmin', 'stores.list.action_change_domain') : Yii::t('app/superadmin', 'stores.list.action_add_domain'), Url::toRoute(['/stores/change-domain', 'id' => $store['id']]), [
                                'class' => 'dropdown-item change-domain',
                                'data-domain' => htmlspecialchars_decode($store['store_domain']),
                                'data-subdomain' => $store['subdomain'],
                                'data-title' => !empty($store['store_domain']) ? Yii::t('app/superadmin', 'stores.list.action_change_domain') : Yii::t('app/superadmin', 'stores.list.action_add_domain')
                            ])?>
                            <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_edit_store'), Url::toRoute(['/stores/edit-store', 'id' => $store['id']]), [
                                'class' => 'dropdown-item edit-store',
                                'data-details' => Json::encode([
                                        'name' => htmlspecialchars_decode($store['name']),
                                    'customer_id' => $store['customer_id'],
                                    'customer_email' => htmlspecialchars_decode($store['customer_email']),
                                    'currency' => htmlspecialchars_decode($store['currency']),
                                    'isOurDomain' => $store['isOurDomain']
                                ])
                            ])?>
                            <?php if (Stores::STATUS_ACTIVE == $store['status']) : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_freeze_store'),
                                    Url::toRoute(['/stores/change-status']),
                                    [
                                        'class' => 'dropdown-item stores-change-status',
                                        'data-params' => ['id' => $store['id'], 'status' => Stores::STATUS_FROZEN],
                                        'data-title' => Yii::t('app/superadmin', 'stores.modal.confirm_freeze')
                                    ])?>
                            <?php elseif (Stores::STATUS_FROZEN == $store['status']) : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_activate_store'),
                                    Url::toRoute(['/stores/change-status']),
                                    ['class' => 'dropdown-item',  'data-method' => 'POST', 'data-params' => ['id' => $store['id'], 'status' => Stores::STATUS_ACTIVE]])?>
                            <?php endif; ?>
                            <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_edit_expiry'), Url::toRoute(['/stores/edit-expiry', 'id' => $store['id']]), [
                                'class' => 'dropdown-item edit-expiry',
                                'data-expired' => htmlspecialchars_decode($store['expired_datetime']),
                            ])?>

                            <?php if(Stores::STATUS_FROZEN == $store['status']): ?>
                                <?= Html::a(Yii::t('app/superadmin', 'stores.list.action_terminate'),
                                    Url::toRoute(['/stores/change-status']),
                                    [
                                        'class' => 'dropdown-item stores-change-status',
                                        'data-params' => ['id' => $store['id'], 'status' => Stores::STATUS_TERMINATED],
                                        'data-title' => Yii::t('app/superadmin', 'stores.modal.confirm_terminate')
                                    ])?>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    </tbody>
</table>

<div class="row">
    <div class="col-md-6">
        <!-- Pagination Start -->
        <nav>
            <ul class="pagination">
                <?= LinkPager::widget([
                    'pagination' => $stores['pages'],
                ]); ?>
            </ul>
        </nav>
        <!-- Pagination End -->
    </div>
    <div class="col-md-6 text-md-right">
        <?= CountPagination::widget([
            'pages' => $stores['pages'],
            'params' => $filters,
        ]) ?>
    </div>
</div>
