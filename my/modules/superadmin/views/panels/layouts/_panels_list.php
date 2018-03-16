<?php
    /* @var $this yii\web\View */
    /* @var $panels \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $panel \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $plans */

    use my\helpers\Url;
    use yii\helpers\Html;
    use common\models\panels\Project;
    use yii\helpers\Json;
    use yii\helpers\ArrayHelper;

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
            <th class="text-nowrap">
                <div class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Plan</a>
                    <div class="dropdown-menu">
                        <?php foreach ($plans as $plan => $label) : ?>
                            <?php $plan = is_numeric($plan) ? (int)$plan : null ?>
                            <a class="dropdown-item <?=($plan === $filters['plan'] ? 'active' : '')?>" href="<?=Url::toRoute(array_merge(['/panels'], $filters, ['plan' => $plan]))?>"><?= $label ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </th>
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
                    $can = $panel['can'];
                    $forecastColor = '';

                    if ($panel['forecast_count'] > $panel['before_orders']) {
                        $forecastColor = 'text-danger';
                    } else if ($panel['forecast_count'] < $panel['of_orders']) {
                        $forecastColor = 'text-orange';
                    }
                ?>
                <tr>
                    <td><?= $panel['id'] ?></td>
                    <td><?= $panel['site'] ?> <?= ($panel['referrer_id'] ? '(r)' : '')?></td>
                    <td><?= $panel['currency'] ?></td>
                    <td class="text-nowrap"><?= strtoupper($panel['lang']) ?></td>
                    <td>
                        <?php if ($panel['cid']) : ?>
                            <a href="<?= Url::toRoute('/customers#' . $panel['cid']); ?>"><?= $panel['customer_email'] ?></a>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap"><?= $panel['tariff'] ?></td>
                    <td><?= $panel['last_count'] ?></td>
                    <td <?=( $forecastColor ? 'class="' . $forecastColor . '"'  : '')?>>
                        <?= $panel['current_count'] ?>
                    </td>
                    <td <?= ( $forecastColor ? 'class="' . $forecastColor . '"'  : '') ?>>
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
                                <?= Html::a('Edit details', Url::toRoute(['/panels/edit', 'id' => $panel['id']]), [
                                    'class' => 'dropdown-item',
                                ])?>
                                <?= Html::a('Edit providers', Url::toRoute(['/panels/edit-providers', 'id' => $panel['id']]), [
                                    'class' => 'dropdown-item edit-providers',
                                    'data-providers' => Json::encode($panel['providers'])
                                ])?>
                                <?= Html::a('Edit expiry date', Url::toRoute(['/panels/edit-expiry', 'id' => $panel['id']]), [
                                    'class' => 'dropdown-item edit-expiry',
                                    'data-expired' => $panel['expired_datetime']
                                ])?>
                                <?= Html::a('Change domain', Url::toRoute(['/panels/change-domain', 'id' => $panel['id']]), [
                                    'class' => 'dropdown-item change-domain',
                                    'data-domain' => $panel['site'],
                                    'data-subdomain' => $panel['subdomain']
                                ])?>
                                <?php if (Project::STATUS_ACTIVE == $panel['act']) : ?>
                                    <?= Html::a('Freeze panel', Url::toRoute(['/panels/change-status', 'id' => $panel['id'], 'status' => Project::STATUS_FROZEN]), ['class' => 'dropdown-item'])?>
                                <?php elseif (Project::STATUS_FROZEN == $panel['act']) : ?>
                                    <?= Html::a('Activate panel', Url::toRoute(['/panels/change-status', 'id' => $panel['id'], 'status' => Project::STATUS_ACTIVE]), ['class' => 'dropdown-item'])?>
                                <?php elseif (Project::STATUS_TERMINATED == $panel['act']) : ?>
                                    <?= Html::a('Restore panel', Url::toRoute(['/panels/change-status', 'id' => $panel['id'], 'status' => Project::STATUS_FROZEN]), ['class' => 'dropdown-item'])?>
                                <?php endif; ?>

                                <?php if ($can['downgrade']) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'panels.list.action_downgrade'), Url::toRoute(['/panels/downgrade', 'id' => $panel['id']]), [
                                        'class' => 'dropdown-item downgrade',
                                        'data-providersurl' => Url::toRoute(['/panels/providers', 'id' => $panel['id']])
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