<?php
    /* @var $this yii\web\View */
    /* @var \common\models\sommerces\Stores  $stores[] */
    /* @var \common\models\sommerces\Stores $store */
    /* @var $accesses */

    use control_panel\helpers\Url;
    use common\models\sommerces\Stores;
    use common\models\sommerces\Orders;
    use yii\bootstrap\Html;

    $storeColors = [
        Stores::STATUS_FROZEN => 'text-danger',
        Stores::STATUS_ACTIVE => 'text-success',
        Stores::STATUS_TERMINATED => 'text-muted',
    ];

    $orderColors = [
        Orders::STATUS_PAID => '',
        Orders::STATUS_ERROR => '',
        Orders::STATUS_PENDING => '',
        Orders::STATUS_CANCELED => 'text-muted',
    ];

    $colors = function($store) use ($storeColors, $orderColors) {
        if ('order' == $store['type']) {
            return $orderColors[$store['status']];
        } else {
            return $storeColors[$store['status']];
        }
    };

    $this->context->addModule('storeController');
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header">
            <?= Yii::t('app', 'stores.list.header')?>
                <a href="<?= Url::toRoute('stores/order') ?>" class="btn btn-outline btn-success create-order" <?= $accesses['canCreate'] ? '' : 'data-error="Orders limit exceeded."' ?>>
                    <?= Yii::t('app', 'stores.list.order_store')?>
                </a>
            <div class="alert alert-danger error-hint hidden" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="content"></span>
            </div>
        </h2>
    </div>
</div>
<?php if (!empty($stores)): ?>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'stores.list.column_domain')?></th>
                        <th><?= Yii::t('app', 'stores.list.column_created')?></th>
                        <th><?= Yii::t('app', 'stores.list.column_expiry')?></th>
                        <th><?= Yii::t('app', 'stores.list.column_status')?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stores as $store): ?>
                        <?php if(!$store['hide']): ?>
                            <tr>
                                <td><?= $store['domain'] ?></td>
                                <td>
                                    <?= $store['date']; ?>
                                </td>
                                <td>
                                    <?= $store['expiredDate']; ?>
                                </td>
                                <td class="<?= $colors($store) ?>">
                                    <?= $store['statusName'] ?>
                                </td>
                                <td>
                                    <?php if ($store['type'] === 'store'): ?>

                                        <?php if ($store['access']['canDashboard']) : ?>
                                            <?= Html::a('<i class="fa fa-external-link fa-fw"></i> ' . Yii::t('app', 'stores.list.action_dashboard'), $store['store_admin_url'], [
                                                'class' => 'btn btn-outline btn-primary btn-xs',
                                                'target' => '_blank'
                                            ])?>
                                        <?php else : ?>
                                            <?= Html::tag('span', '<i class="fa fa-external-link fa-fw"></i> ' . Yii::t('app', 'stores.list.action_dashboard'), [
                                                'class' => 'btn btn-outline btn-default btn-xs disabled',
                                            ])?>
                                        <?php endif; ?>

                                        <?php if ($store['access']['canStaffView']) : ?>
                                            <?= Html::a('<i class="fa fa-user fa-fw"></i> ' . Yii::t('app', 'stores.list.action_staff'), '/stores/staff/' . $store['id'], [
                                                'class' => 'btn btn-outline btn-info btn-xs',
                                            ])?>
                                        <?php endif; ?>

                                        <?php if ($store['access']['canDomainConnect']) : ?>
                                            <?= Html::a('<i class="fa fa-globe fa-fw"></i> ' . Yii::t('app', 'stores.list.action_domain_connect'), [
                                                '/store/edit-domain',
                                                'id' => $store['id']
                                            ], [
                                                'class' => 'btn btn-outline btn-purple btn-xs edit-store-domain',
                                                'data-domain' => $store['store_domain']
                                            ])?>
                                        <?php else : ?>
                                            <?= Html::tag('span', '<i class="fa fa-globe fa-fw"></i> ' . Yii::t('app', 'stores.list.action_domain_connect'), [
                                                'class' => 'btn btn-outline btn-default btn-xs disabled',
                                            ])?>
                                        <?php endif; ?>
                                        
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->render('layouts/_edit_domain_modal'); ?>