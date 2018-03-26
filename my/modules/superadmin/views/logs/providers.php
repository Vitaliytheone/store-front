<?php
    /* @var $this yii\web\View */
    /* @var $logs \my\modules\superadmin\models\search\ProviderLogsSearch */
    /* @var $log \common\models\panels\SearchProcessor */
    /* @var $filters */


use yii\widgets\LinkPager;
    use my\helpers\Url;

    $this->context->addModule('superadminProviderLogsController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3 justify-content-end">
        <li>
            <form class="form-inline" method="GET" id="logsSearch" action="<?=Url::toRoute(array_merge(['/logs/providers'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'logs.providers.list.search') ?>" value="<?=$filters['query']?>">
                    <span class="input-group-btn">
                    <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                </span>
                </div>
            </form>
        </li>
    </ul>

    <table class="table table-border">
        <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'logs.providers.list.column_site')?></th>
            <th><?= Yii::t('app/superadmin', 'logs.providers.list.column_admin')?></th>
            <th><?= Yii::t('app/superadmin', 'logs.providers.list.column_provider')?></th>
            <th><?= Yii::t('app/superadmin', 'logs.providers.list.column_result')?></th>
            <th><?= Yii::t('app/superadmin', 'logs.providers.list.column_date')?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($logs['models'])) : ?>
            <?php foreach ($logs['models'] as $log) : ?>
                <tr>
                    <td>
                        <?= isset($log->project) ? $log->project->site : '' ?>
                    </td>
                    <td>
                        <?= isset($log->admin) ? $log->admin->login : '' ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($log->search) ?>
                    </td>
                    <td>
                        <?= $log->getResult() ?>
                    </td>
                    <td>
                    <span class="text-nowrap">
                        <?= $log->getFormattedDate('date', 'php:Y-m-d') ?>
                    </span>
                        <?= $log->getFormattedDate('date', 'php:H:i:s') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>

    <div class="text-align-center pager">
        <?= LinkPager::widget([
            'pagination' => $logs['pages'],
        ]); ?>
    </div>

</div>
