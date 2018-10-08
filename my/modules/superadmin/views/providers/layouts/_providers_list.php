<?php
    /* @var $this yii\web\View */
    /* @var $providers \my\modules\superadmin\models\search\ProvidersSearch */
    /* @var $provider \common\models\panels\AdditionalServices */
    /* @var $scripts array */

    use my\helpers\Url;
    use yii\helpers\Html;
    use yii\helpers\Json;
    use my\helpers\SpecialCharsHelper;
    use yii\widgets\LinkPager;
    use my\modules\superadmin\widgets\CountPagination;
?>
<table class="table table-sm table-custom" id="providersTable">
    <thead>
    <tr>
        <th class="query-sort"><?= $providers['sort']->link('provider_id', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('name', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th><?= $providers['sort']->link('service_count', ['class' => 'sort_link', 'style' => 'color:inherit']) ?></th>
        <th><?= $providers['sort']->link('service_inuse_count', ['class' => 'sort_link', 'style' => 'color:inherit']) ?></th>
        <th class="query-sort"><?= $providers['sort']->link('start_count', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('refill', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('cancel', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('service_view', ['class' => 'sort_link', 'style' => 'color:inherit']);?></th>
        <th class="query-sort"><?= $providers['sort']->link('send_method', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="query-sort"><?= $providers['sort']->link('type', ['class' => 'sort_link', 'style' => 'color:inherit']); ?></th>
        <th class="table-custom__dropdown">
            <div class="dropdown">
                <a class="btn btn-sm btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong><?= Yii::t('app/superadmin', 'providers.list.column_script')?></strong>
                </a>
                <div class="dropdown-menu dropdown-menu__max" aria-labelledby="dropdownMenuLink">
                    <?php foreach (SpecialCharsHelper::multiPurifier($scripts) as $script) : ?>
                        <a class="dropdown-item <?=($script['name_script'] === $filters['script'] || ($script['name_script'] == 'all' && $filters['script'] == null) ? 'active' : '')?>" href="<?=Url::toRoute(
                            array_merge(['/providers'], $filters, ['script' => $script['name_script']])
                        )?>">
                            <?= $script['string']; ?>
                        </a>
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
            <tr>
                <td>
                    <?= $provider['provider_id'] ?>
                </td>
                <td>
                    <?= $provider['name'] ?>
                </td>
                <td>
                    <?php if ($provider['count']) : ?>
                        <?= Html::a($provider['count'], Url::toRoute(['/providers/get-panels', 'id' => $provider['id']]), [
                            'class' => 'show-panels',
                            'data-projects' => Json::encode($provider['projects']),
                            'data-header' => $provider['name'] . ' - count'
                        ])?>
                    <?php else : ?>
                        <?= $provider['count'] ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($provider['in_use']) : ?>
                        <?= Html::a($provider['in_use'], Url::toRoute(['/providers/get-panels', 'id' => $provider['id'], 'use' => 1]), [
                            'class' => 'show-panels',
                            'data-projects' => Json::encode($provider['usedProjects']),
                            'data-header' => $provider['name'] . ' - in use'
                        ])?>
                    <?php else : ?>
                        <?= $provider['in_use'] ?>
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
                    <?= $provider['status'] ?>
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
                                'class' => 'dropdown-item edit-provider',
                                'data-details' => Json::encode($provider['form_data'])
                            ])?>
                            <?= Html::a(Yii::t('app/superadmin', 'providers.modal_clone_provider'), Url::toRoute(['/providers/create']), [
                                'class' => 'dropdown-item clone-provider',
                                'data-details' => Json::encode($provider['form_data'])
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
                <?= LinkPager::widget(['pagination' => $providers['pages'],]); ?>
            </ul>
        </nav>
    </div>
    <div class="col-md-6 text-md-right">
        <?= CountPagination::widget([
            'pages' => $providers['pages'],
            'params' => $filters
        ]) ?>
    </div>
</div>