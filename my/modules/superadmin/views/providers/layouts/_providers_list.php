<?php
    /* @var $this yii\web\View */
    /* @var $providers \my\modules\superadmin\models\search\ProvidersSearch */
    /* @var $provider \common\models\panels\AdditionalServices */

    use my\helpers\Url;
    use yii\helpers\Html;
    use yii\helpers\Json;
    use common\models\panels\AdditionalServices;
    use my\helpers\SpecialCharsHelper;
    use yii\widgets\LinkPager;
    use my\modules\superadmin\widgets\CountPagination;
?>
<table class="table table-sm table-custom" id="providersTable">
    <thead>
    <tr>
        <th class="query-sort"><?= $providers['sort']->link('res', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('name', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_count')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_in_use')?></th>
        <th class="query-sort"><?= $providers['sort']->link('sc', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('refill', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('cancel', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('service_view', ['class' => 'sort_link', 'style' => 'color:inherit']);?></th>
        <th class="query-sort"><?= $providers['sort']->link('auto_order', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('type', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="table-custom__dropdown">
            <div class="dropdown">
                <a class="btn btn-sm btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong><?= Yii::t('app/superadmin', 'panels.plan')?></strong>
                </a>
                <div class="dropdown-menu dropdown-menu__max" aria-labelledby="dropdownMenuLink">
                    <?php foreach ($plans as $plan => $label) : ?>
                        <a class="dropdown-item <?//=($plan === $filters['plan'] ? 'active' : '')?>" href="<?//=Url::toRoute(array_merge(['/panels'], $filters, ['plan' => $plan, 'page_size' => $pageSize]))?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </th>
        <th class="query-sort"><?= $providers['sort']->link('status', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('date', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="table-custom__action-th"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($providers['models'])) : ?>
        <?php foreach (SpecialCharsHelper::multiPurifier($providers['models']) as $key => $provider) : ?>
            <?php
                $count = count($provider['projects']);
                $use = count($provider['usedProjects']);
            ?>
            <tr>
                <td>
                    <?= $provider['res'] ?>
                </td>
                <td>
                    <?= $provider['name'] ?>
                </td>
                <td>
                    <?php if ($count) : ?>
                        <?= Html::a($count, Url::toRoute(['/providers/get-panels', 'id' => $provider['id']]), [
                            'class' => 'show-panels',
                            'data-projects' => Json::encode($provider['projects']),
                            'data-header' => $provider['name'] . ' - count'
                        ])?>
                    <?php else : ?>
                        <?= $count ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($use) : ?>
                        <?= Html::a($use, Url::toRoute(['/providers/get-panels', 'id' => $provider['id'], 'use' => 1]), [
                            'class' => 'show-panels',
                            'data-projects' => Json::encode($provider['usedProjects']),
                            'data-header' => $provider['name'] . ' - in use'
                        ])?>
                    <?php else : ?>
                        <?= $use ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $provider['sc'] ?>
                </td>
                <td>
                    <?= $provider['refill'] ?>
                </td>
                <td>
                    <?= $provider['cancel'] ?>
                </td>
                <td>
                    <?= $provider['service_view'] ?>
                </td>
                <td>
                    <?= $provider['auto_order'] ?>
                </td>
                <td>
                    <?= $provider['type'] ?>
                </td>
                <td>

                </td>
                <td>
                    <?= $provider['statusName'] ?>
                </td>
                <td>
                    <?= $provider['date'] > 0 ? $provider['date'] : '' ?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'providers.list.actions_label')?></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <h6 class="dropdown-header"><?= Yii::t('app/superadmin', 'providers.list.action_change_status') ?></h6>

                            <?php if (AdditionalServices::STATUS_ACTIVE != $provider['status']) : ?>
                                <?= Html::a(Yii::t('app', 'additional_service.status.ok'), Url::toRoute(['/providers/change-status',
                                    'id' => $provider['id'],
                                    'status' => AdditionalServices::STATUS_ACTIVE
                                ]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>

                            <?php if (AdditionalServices::STATUS_FROZEN != $provider['status']) : ?>
                                <?= Html::a(Yii::t('app', 'additional_service.status.broken'), Url::toRoute(['/providers/change-status',
                                    'id' => $provider['id'],
                                    'status' => AdditionalServices::STATUS_FROZEN
                                ]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>

                            <?php if (AdditionalServices::STATUS_PROCESSING != $provider['status']) : ?>
                                <?= Html::a(Yii::t('app', 'additional_service.status.send_only'), Url::toRoute(['/providers/change-status',
                                    'id' => $provider['id'],
                                    'status' => AdditionalServices::STATUS_PROCESSING
                                ]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>

                            <?php if (AdditionalServices::STATUS_NOT_UPDATED != $provider['status']) : ?>
                                <?= Html::a(Yii::t('app', 'additional_service.status.not_updated'), Url::toRoute(['/providers/change-status',
                                    'id' => $provider['id'],
                                    'status' => AdditionalServices::STATUS_NOT_UPDATED
                                ]), [
                                    'class' => 'dropdown-item',
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
<!-- Delete <br> after update ccs to v.2 -->
<br>
<!-- -->
<!-- Add pagination widgets -->