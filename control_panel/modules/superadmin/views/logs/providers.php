<?php
    /* @var $this yii\web\View */
    /* @var $logs \superadmin\models\search\ProviderLogsSearch */
    /* @var $log \common\models\panels\SearchProcessor */
    /* @var $filters */


use yii\widgets\LinkPager;
    use control_panel\helpers\Url;

    $this->context->addModule('superadminProviderLogsController');
?>
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="ml-auto">
            <form class="form" method="GET" id="logsSearch" action="<?=Url::toRoute(array_merge(['/logs/providers'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'logs.providers.list.search') ?>" value="<?=$filters['query']?>">
                    <div class="input-group-append">
                        <button class="btn btn-light" type="submit"><span id="submitSearch" class="fa fa-search"></span></button>
                    </div>
                </div>
            </form>
        </li>
    </ul>

    <table class="table table-sm table-custom">
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

<div class="row">
    <div class="col-md-6">
        <nav>
            <ul class="pagination">
                <?= LinkPager::widget(['pagination' => $logs['pages'],]); ?>
            </ul>
        </nav>
    </div>
</div>

