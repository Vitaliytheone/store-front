<?php
    /* @var $this yii\web\View */
    /* @var $customers array */
    /* @var $customer \my\modules\superadmin\models\search\CustomersSearch */
    /* @var $filters array */

    use yii\helpers\Html;
    use common\models\panels\Customers;
    use my\helpers\Url;
    use yii\helpers\Json;
    use yii\widgets\LinkPager;
    use my\modules\superadmin\widgets\CountPagination;
?>


        <table class="table table-sm table-custom">
            <thead class="">
            <tr>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_id') ?></th>
                <th class="table-custom__customer-th"><?= Yii::t('app/superadmin', 'customers.list.header_email') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_panels') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_stores') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_child') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_domains') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_certificates') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_first_name') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_last_name') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_status') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_created') ?></th>
                <th><?= Yii::t('app/superadmin', 'customers.list.header_last_auth') ?></th>
                <th class="table-custom__action-th"></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($customers['models'])) : ?>
                <?php foreach ($customers['models'] as $customer) : ?>
                    <tr id="<?= $customer->id ?>">
                        <td><?= $customer->id ?></td>
                        <td class="table-custom__customer-td">
                            <?php $referralView = Html::a(
                                Html::tag(
                                    'span',
                                    '',
                                    [
                                        'class' => 'my-icons my-icons-referral',
                                        'data-placement' => 'top',
                                    ]
                                ), Url::toRoute(['/customers', 'id' => $customer->referrer_id]), ['target' => '_blank']
                            );  ?>
                            <?= $customer->email ?> <?= ($customer->referrer_id ? ' ' . $referralView : '')?>
                            <a href="<?= Url::toRoute(['/customers/auth', 'id' => $customer->id]) ?>" class="table-custom__customer-button" target="_blank" data-placement="top">
                                <span class="my-icons my-icons-autorization"></span>
                            </a>
                        </td>
                        <td>
                            <?= Html::a($customer->countProjects, Url::toRoute(['/panels', 'customer_id' => $customer->id])); ?>
                        </td>
                        <td>
                            <?php if (!$customer->can('stores')) : ?>
                                <?= Html::a(Html::tag('span', Yii::t('app/superadmin', 'customers.list.activate_stores'),
                                    ['class' => 'badge badge-light']),
                                    Url::toRoute(['/customers/activate-stores']),
                                    ['data-method' => 'POST', 'data-params' => ['id' => $customer->id]]
                                )?>
                            <?php else : ?>
                                <?= Html::a($customer->countStores, Url::toRoute(['/stores', 'customer_id' => $customer->id])); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= Html::a($customer->countChild, Url::toRoute(['/child-panels', 'customer_id' => $customer->id])); ?>
                        </td>
                        <td>
                            <?php if ($customer->buy_domain == 0) : ?>
                                <?= Html::a(Html::tag('span', Yii::t('app/superadmin', 'customers.list.activate_stores'),
                                    ['class' => 'badge badge-light']),
                                    Url::toRoute(['/customers/activate-domain']),
                                    ['data-method' => 'POST', 'data-params' => ['id' => $customer->id]]
                                )?>
                            <?php else : ?>
                                <?= Html::a($customer->countDomains, Url::toRoute(['/domains', 'customer_id' => $customer->id])); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= Html::a($customer->countSslCerts, Url::toRoute(['/ssl', 'customer_id' => $customer->id])); ?>
                        </td>
                        <td><?= htmlspecialchars($customer->first_name) ?></td>
                        <td><?= htmlspecialchars($customer->last_name) ?></td>
                        <td><?= $customer->getStatusName() ?></td>
                        <td>
                        <span class="table-custom__date">
                            <?= $customer->getFormattedDate('date_create', 'php:Y-m-d') ?>
                        </span>
                        <span class="table-custom__time">
                            <?= $customer->getFormattedDate('date_create', 'php:H:i:s') ?>
                        </span>
                        </td>
                        <td>
                        <span class="table-custom__date">
                            <?= $customer->getFormattedDate('auth_date', 'php:Y-m-d') ?>
                        </span>
                        <span class="table-custom__time">
                            <?= $customer->getFormattedDate('auth_date', 'php:H:i:s') ?>
                        </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><?= Yii::t('app/superadmin', 'customers.dropdown.actions_label') ?></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <?= Html::a(Yii::t('app/superadmin', 'customers.dropdown.set_password_modal'), Url::toRoute(['/customers/set-password', 'id' => $customer->id]), [
                                        'class' => 'dropdown-item set-password',
                                    ])?>
                                    <?= Html::a(Yii::t('app/superadmin', 'customers.dropdown.edit_customer_modal'), Url::toRoute(['/customers/edit', 'id' => $customer->id]), [
                                        'class' => 'dropdown-item edit',
                                        'data-details' => Json::encode($customer->getAttributes(['email', 'referral_status']))
                                    ])?>
                                    <?php if (Customers::STATUS_ACTIVE == $customer->status) : ?>
                                        <?= Html::a(Yii::t('app/superadmin', 'customers.dropdown.suspend_btn'),
                                            Url::toRoute('/customers/change-status'),
                                            ['class' => 'dropdown-item', 'data-method' => 'POST', 'data-params' => ['id' => $customer->id, 'status' => Customers::STATUS_SUSPENDED]]
                                            )?>
                                    <?php elseif (Customers::STATUS_SUSPENDED == $customer->status) : ?>
                                        <?= Html::a(Yii::t('app/superadmin', 'customers.dropdown.activate_btn'),
                                            Url::toRoute('/customers/change-status'),
                                            ['class' => 'dropdown-item', 'data-method' => 'POST', 'data-params' => ['id' => $customer->id, 'status' => Customers::STATUS_ACTIVE]]
                                        )?>
                                    <?php endif; ?>

                                    <?= Html::a(Yii::t('app/superadmin', 'customers.dropdown.sign_btn'), Url::toRoute(['/customers/auth', 'id' => $customer->id]), [
                                        'class' => 'dropdown-item',
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


        <div class="row">
            <div class="col-md-6">
                <nav>
                    <ul class="pagination">
                        <?= LinkPager::widget(['pagination' => $customers['pages'],]); ?>
                    </ul>
                </nav>
                <!-- Pagination End -->
            </div>
            <div class="col-md-6 text-md-right">
                <?= CountPagination::widget([
                    'pages' => $customers['pages'],
                    'params' => $filters
                ]) ?>
            </div>
        </div>
