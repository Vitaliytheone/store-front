<?php
    /* @var $this yii\web\View */
    /* @var $customers \my\modules\superadmin\models\search\CustomersSearch */
    /* @var $customer \my\modules\superadmin\models\search\CustomersSearch */

    use yii\helpers\Html;
    use common\models\panels\Customers;
    use my\helpers\Url;
    use yii\helpers\Json;
?>
<table class="table table-border">
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Panels</th>
            <th>Child</th>
            <th>Domains</th>
            <th>Certificates</th>
            <th>First name</th>
            <th>Last name</th>
            <th>Status</th>
            <th class="text-nowrap">Created</th>
            <th class="text-nowrap">Last auth</th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($customers['models'])) : ?>
            <?php foreach ($customers['models'] as $customer) : ?>
                <tr id="<?= $customer->id ?>">
                    <td><?= $customer->id ?></td>
                    <td><?= $customer->email ?> <?= ($customer->referrer_id ? '(r)' : '')?></td>
                    <td>
                        <?= Html::a($customer->countProjects, Url::toRoute(['/panels', 'customer_id' => $customer->id])); ?>
                    </td>
                    <td>
                        <?= Html::a($customer->countChild, Url::toRoute(['/child-panels', 'customer_id' => $customer->id])); ?>
                    </td>
                    <td>
                        <?= Html::a($customer->countDomains, Url::toRoute(['/domains', 'customer_id' => $customer->id])); ?>
                    </td>
                    <td>
                        <?= Html::a($customer->countSslCerts, Url::toRoute(['/ssl', 'customer_id' => $customer->id])); ?>
                    </td>
                    <td><?= htmlspecialchars($customer->first_name) ?></td>
                    <td><?= htmlspecialchars($customer->last_name) ?></td>
                    <td><?= $customer->getStatusName() ?></td>
                    <td>
                        <span class="text-nowrap">
                            <?= $customer->getFormattedDate('date_create', 'php:Y-m-d') ?>
                        </span>
                        <?= $customer->getFormattedDate('date_create', 'php:H:i:s') ?>
                    </td>
                    <td>
                        <span class="text-nowrap">
                            <?= $customer->getFormattedDate('auth_date', 'php:Y-m-d') ?>
                        </span>
                        <?= $customer->getFormattedDate('auth_date', 'php:H:i:s') ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?= Html::a('Set password', Url::toRoute(['/customers/set-password', 'id' => $customer->id]), [
                                    'class' => 'dropdown-item set-password',
                                ])?>
                                <?= Html::a('Edit customer', Url::toRoute(['/customers/edit', 'id' => $customer->id]), [
                                    'class' => 'dropdown-item edit',
                                    'data-details' => Json::encode($customer->getAttributes(['email', 'first_name', 'last_name']))
                                ])?>
                                <?php if (Customers::STATUS_ACTIVE == $customer->status) : ?>
                                    <?= Html::a('Suspend customer', Url::toRoute(['/customers/change-status', 'id' => $customer->id, 'status' => Customers::STATUS_SUSPENDED]), [
                                        'class' => 'dropdown-item change-status',
                                    ])?>
                                <?php elseif (Customers::STATUS_SUSPENDED == $customer->status) : ?>
                                    <?= Html::a('Activate customer', Url::toRoute(['/customers/change-status', 'id' => $customer->id, 'status' => Customers::STATUS_ACTIVE]), [
                                        'class' => 'dropdown-item change-status',
                                    ])?>
                                <?php endif; ?>

                                <?php if ($customer->can('enable_referral')) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'customers.list.enable_referral_action'), Url::toRoute(['/customers/enable-referral', 'id' => $customer->id]), [
                                        'class' => 'dropdown-item',
                                    ])?>
                                <?php elseif ($customer->can('disable_referral')) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'customers.list.disable_referral_action'), Url::toRoute(['/customers/disable-referral', 'id' => $customer->id]), [
                                        'class' => 'dropdown-item',
                                    ])?>
                                <?php endif; ?>

                                <?= Html::a('Sign in as customer', Url::toRoute(['/customers/auth', 'id' => $customer->id]), [
                                    'class' => 'dropdown-item auth',
                                    'target' => '_blank'
                                ])?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

    </tbody>
</table>