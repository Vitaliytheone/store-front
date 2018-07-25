<?php
    /* @var $this yii\web\View */
    /* @var $panels \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $panel \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $plans */
    /* @var $pageSizes array */
    /* @var $filters array */
    /* @var $action string */
    /* @var $pageSize */

    use my\helpers\Url;
    use yii\widgets\LinkPager;
    use my\modules\superadmin\widgets\CountPagination;

    //$pageSize = $panels['pages']->pageSize;
    $now = time();
?>
<div class="tab-pane fade show active" id="status-all" role="tabpanel">
    <table class="table table-sm table-custom">
        <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'panels.table.title.id')?></th>
            <th><?= Yii::t('app/superadmin', 'panels.table.title.domain')?></th>
            <th><?= Yii::t('app/superadmin', 'panels.table.title.currency')?></th>
            <th class="table-custom__languages-th"><?= Yii::t('app/superadmin', 'panels.table.title.language')?></th>
            <th class="table-custom__customer-th"><?= Yii::t('app/superadmin', 'panels.table.title.customer')?></th>
            <?php if ($action == 'panels') : ?>
                <th class="table-custom__dropdown">
                    <div class="dropdown">
                        <a class="btn btn-sm btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <strong><?= Yii::t('app/superadmin', 'panels.plan')?></strong>
                        </a>
                        <div class="dropdown-menu dropdown-menu__max" aria-labelledby="dropdownMenuLink">
                            <?php foreach ($plans as $plan => $label) : ?>
                                <?php $plan = is_numeric($plan) ? (int)$plan : null ?>
                                <a class="dropdown-item <?=($plan === $filters['plan'] ? 'active' : '')?>" href="<?=Url::toRoute(array_merge(['/panels'], $filters, ['plan' => $plan, 'page_size' => $pageSize]))?>"><?= $label ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </th>
            <?php endif; ?>
            <th><?= Yii::t('app/superadmin', 'panels.table.title.orders')?></th>
            <th><?= Yii::t('app/superadmin', 'panels.table.title.status')?></th>
            <th class="text-nowrap"><?= Yii::t('app/superadmin', 'panels.table.title.created')?></th>
            <th class="text-nowrap"><?= Yii::t('app/superadmin', 'panels.table.title.expiry')?></th>
            <th class="w-1"></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($panels['models'])) : ?>
            <?php foreach ($panels['models'] as $panel) : ?>
                <?php
                    $forecastColor = '';
                    $forecastPlanColor = '';
                    if ($panel['plan']!= $panel['tariffId']) {
                        if ($panel['forecast_count'] > $panel['before_orders'] && $panel['plan'] > 0) {
                            $forecastColor = 'text-warning';
                            $forecastPlanColor = 'table-custom__forecast-plan-bottom';
                        } else if ($panel['forecast_count'] < $panel['of_orders'] && $panel['plan'] > 0) {
                            $forecastColor = 'text-success';
                            $forecastPlanColor = 'text-success';
                        }
                    }
                    $loginUrl = Url::toRoute(['/panels/sign-in-as-admin', 'id' => $panel['id']]);
                ?>
                <tr>
                    <td><?= $panel['id'] ?></td>
                    <td class="table-no-wrap table-custom__customer-td">
                        <?= $panel['site'] ?>
                        <?php if ($panel['referrer_id']) :?>
                            <a href="<?= Url::toRoute(['/customers', 'query' => $panel['referrer_email']]) ?>" target="_blank">
                                <span class="my-icons my-icons-referral" data-placement="top" title=""></span>
                            </a>
                        <?php endif; ?>
                        <a href="<?= $loginUrl ?>" target="_blank" class="table-custom__customer-button"  data-placement="top" title="">
                            <span class="my-icons my-icons-autorization"></span>
                        </a>
                    </td>
                    <td><?= $panel['currency_code'] ?></td>
                    <td class="text-nowrap"><?= strtoupper($panel['lang']) ?></td>
                    <td>
                        <?php if ($panel['cid']) : ?>
                            <a href="<?= Url::toRoute(['/customers', 'query' => $panel['customer_email']]); ?>" target="_blank"><?= $panel['customer_email'] ?></a>
                        <?php endif; ?>
                    </td>
                    <?php if ($action == 'panels') : ?>
                        <td class="text-nowrap">
                            <div class="table-custom__current-plan"><?= $panel['tariff'] ?></div>
                            <?php if ($panel['plan'] != 0 && $panel['plan']!= $panel['tariffId'] && $panel['tariffId'] > 0) : ?>
                                <div class="<?= $forecastPlanColor ?>"><?= $panel['futureTariff'] ?></div>
                            <?php endif ?>
                        </td>
                    <?php endif;  ?>


                    <td class="table-no-wrap"><?= $panel['last_count'] ?> /
                        <span class="<?= $forecastColor ?>"><?= $panel['current_count'] ?></span> /
                        <span class="<?= $forecastColor ?>"> <?= $panel['forecast_count'] ?></span>
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
                        <?php if ($panel['no_invoice']) : ?>
                            <i class="fa fa-ban" aria-hidden="true"></i>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= Yii::t('app/superadmin', 'panels.list.actions')?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?php if ($action == 'panels') : ?>
                                    <?= $this->render('_panels_actions', ['panel' => $panel]) ?>
                                <?php else : ?>
                                    <?= $this->render('_child_panels_actions', ['panel' => $panel]) ?>
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
            <?= LinkPager::widget([
                'pagination' => $panels['pages'],
            ]); ?>
        </div>
        <div class="col-md-6 text-md-right">
            <?= CountPagination::widget([
                'pages' => $panels['pages'],
                'params' => array_merge($filters, ['page_size' => null])
            ]) ?>
        </div>
    </div>
</div>

