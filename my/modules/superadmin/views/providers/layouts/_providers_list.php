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
        <th class="query-sort"><?= $providers['sort']->link('provider_id', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('name', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_count')?></th>
        <th><?= Yii::t('app/superadmin', 'providers.list.column_in_use')?></th>
        <th class="query-sort"><?= $providers['sort']->link('start_count', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('refill', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('cancel', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('service_view', ['class' => 'sort_link', 'style' => 'color:inherit']);?></th>
        <th class="query-sort"><?= $providers['sort']->link('send_method', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('type', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="table-custom__dropdown">
            <div class="dropdown">
                <a class="btn btn-sm btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong><?= Yii::t('app/superadmin', 'panels.plan')?></strong>
                </a>
                <div class="dropdown-menu dropdown-menu__max" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item <?=($filters['plan'] == 'all' || $filters['plan'] == null ? 'active' : '')?>" href="<?=Url::toRoute(
                        array_merge(['/providers'], $filters, ['plan' => 'all'])
                    )?>">
                        <?= $plans['all']['label'] . ' (' . $plans['all']['count'] . ')'; ?>
                    </a>
                    <?php foreach ($plans as $key => $plan) : ?>
                    <?php if (isset($plan['name_script'])) : ?>
                        <a class="dropdown-item <?=($plan['name_script'] === $filters['plan'] ? 'active' : '')?>" href="<?=Url::toRoute(
                                array_merge(['/providers'], $filters, ['plan' => $plan['name_script']])
                        )?>">
                            <?= $plan['name_script'] . ' (' . $plan['count'] . ')'; ?>
                        </a>
                    <?php endif; ?>
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
                    <?= $provider['provider_id'] ?>
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
                    <?= $provider['start_count'] ?>
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
                    <?= $provider['send_method'] ?>
                </td>
                <td>
                    <?= $provider['type'] ?>
                </td>
                <td>
                    <?= $provider['name_script'] ?>
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

                            <?= Html::a(Yii::t('app/superadmin', 'providers.modal_edit_provider'), Url::toRoute(['/providers/edit',
                                'id' => $provider['id']
                            ]), [
                                'class' => 'dropdown-item edit',
                                'data-details' => Json::encode($provider)
                            ])?>
                            <?= Html::a(Yii::t('app/superadmin', 'providers.modal_clone_provider'), Url::toRoute(['/providers/clone',
                                'id' => $provider['id']
                            ]), [
                                'class' => 'dropdown-item clone',
                                'data-details' => Json::encode($provider)
                            ])?>
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