<?php
    /* @var $this yii\web\View */
    /* @var $panels \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $panel \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $plans */

    use my\helpers\Url;
    use yii\helpers\Html;
    use common\models\panels\Project;
    use yii\helpers\Json;

    $now = time();
?>
<table class="table table-border">
    <thead>
    <tr>
        <th>ID</th>
        <th>Domain</th>
        <th></th>
        <th></th>
        <th>Customer</th>
        <th>Prev</th>
        <th>Curr</th>
        <th>Abt</th>
        <th>Status</th>
        <th class="text-nowrap">Created</th>
        <th class="text-nowrap">Expiry</th>
        <th class="w-1"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($panels['models'])) : ?>
        <?php foreach ($panels['models'] as $panel) : ?>
            <?php
            $loginUrl = Url::toRoute(['/child-panels/sign-in-as-admin', 'id' => $panel['id']]);
            ?>
            <tr>
                <td><?= $panel['id'] ?></td>
                <td>
                    <div class="pull-left">
                        <?= $panel['site'] ?>
                    </div>
                    <div class="pull-right">
                        <a href="<?= $loginUrl ?>" class="login-key-link" target="_blank"><i class="fa fa-key fa-flip-horizontal" aria-hidden="true"></i></a>
                    </div>
                </td>
                <td><?= $panel['currency'] ?></td>
                <td class="text-nowrap"><?= strtoupper($panel['lang']) ?></td>
                <td>
                    <?php if ($panel['cid']) : ?>
                        <a href="<?= Url::toRoute(['/customers', 'id' => $panel['cid']]); ?>" target="_blank"><?= $panel['customer_email'] ?></a>
                    <?php endif; ?>
                </td>
                <td><?= $panel['last_count'] ?></td>
                <td>
                    <?= $panel['current_count'] ?>
                </td>
                <td >
                    <?= $panel['forecast_count'] ?>
                </td>
                <td><?= $panel['status'] ?></td>
                <td>
                        <span class="text-nowrap">
                            <?= $panel['created_date'] ?>
                        </span>
                    <?= $panel['created_time'] ?>
                </td>
                <td <?= ($now > $panel['expired'] ? 'class="text-danger"' : '') ?>>
                        <span class="text-nowrap">
                            <?= $panel['expired_date'] ?>
                        </span>
                    <?= $panel['expired_time'] ?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?= Html::a('Edit details', Url::toRoute(['/child-panels/edit', 'id' => $panel['id']]), [
                                'class' => 'dropdown-item',
                            ])?>
                            <?= Html::a('Edit providers', Url::toRoute(['/child-panels/edit-providers', 'id' => $panel['id']]), [
                                'class' => 'dropdown-item edit-providers',
                                'data-providers' => Json::encode($panel['providers'])
                            ])?>
                            <?= Html::a('Edit expiry date', Url::toRoute(['/child-panels/edit-expiry', 'id' => $panel['id']]), [
                                'class' => 'dropdown-item edit-expiry',
                                'data-expired' => $panel['expired_datetime']
                            ])?>
                            <?= Html::a('Change domain', Url::toRoute(['/child-panels/change-domain', 'id' => $panel['id']]), [
                                'class' => 'dropdown-item change-domain',
                                'data-domain' => $panel['site'],
                                'data-subdomain' => $panel['subdomain']
                            ])?>
                            <?php if (Project::STATUS_ACTIVE == $panel['act']) : ?>
                                <?= Html::a('Freeze panel', Url::toRoute(['/child-panels/change-status', 'id' => $panel['id'], 'status' => Project::STATUS_FROZEN]), ['class' => 'dropdown-item'])?>
                            <?php elseif (Project::STATUS_FROZEN == $panel['act']) : ?>
                                <?= Html::a('Activate panel', Url::toRoute(['/child-panels/change-status', 'id' => $panel['id'], 'status' => Project::STATUS_ACTIVE]), ['class' => 'dropdown-item'])?>
                            <?php elseif (Project::STATUS_TERMINATED == $panel['act']) : ?>
                                <?= Html::a('Restore panel', Url::toRoute(['/child-panels/change-status', 'id' => $panel['id'], 'status' => Project::STATUS_FROZEN]), ['class' => 'dropdown-item'])?>
                            <?php endif; ?>

                            <?= Html::a(Yii::t('app/superadmin', 'child_panels.list.action_upgrade'), Url::toRoute(['/child-panels/upgrade', 'id' => $panel['id']]), [
                                'class' => 'dropdown-item upgrade',
                                'data-total' => 25
                            ])?>

                            <?= Html::a(Yii::t('app/superadmin', 'child_panels.list.sign_in_as_admin'), $loginUrl, [
                                'class' => 'dropdown-item',
                                'target' => '_blank',
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    </tbody>
</table>