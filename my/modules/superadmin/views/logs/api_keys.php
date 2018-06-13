<?php
use yii\widgets\LinkPager;
use my\helpers\Url;

/** @var \yii\data\Pagination $pagination */
/** @var array $logs */
/** @var array $filters */
/** @var array $searchType */

?>

<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code == $filters['status'] ? 'active' : '') ?>" href="<?= ($code != 0 ? Url::toRoute(['/logs/api-keys', 'status' => $code, 'search' => $filters['search']]) : Url::toRoute(['/logs/api-keys'])) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <form class="form-inline" method="GET" id="panelsSearch" action="<?=Url::toRoute(['/logs/api-keys'])?>">
                <div class="input-group input-group__select">
                    <input type="text" class="form-control" name="search" placeholder="Search" value="<?=$filters['search']?>">

                    <div class="form-group__select">
                        <select  name="search-type">
                            <?php foreach ($searchType as $key => $type): ?>
                                <option value="<?php echo $key ?>"<?php if ($filters['search-type'] == $key) echo ' selected' ?>><?php echo $type ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <input type="hidden" name="status" value="<?php echo $filters['status'] ?>">
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
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_id') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_panel') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_account') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_provider') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_key') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_in_use') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_date') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $id => $log): ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= $log['site'] ?></td>
                <td>
                    <?php if($log['admin_login'] === 0): ?>
                    <?php elseif($log['admin_id'] > 99999990): ?>superadmin-id <?= $log['admin_id'] ?>
                    <?php else: ?><?= htmlspecialchars($log['admin_login']) ?>
                    <?php endif; ?>
                </td>
                <td><?= $log['provider'] ?></td>
                <td class="break-all">
                    <?php if(!empty($log['login'])): ?> <?= htmlspecialchars($log['login']) ?> <br> <?php endif; ?>
                    <?php if(!empty($log['passwd'])): ?> <?= htmlspecialchars($log['passwd']) ?> <br> <?php endif; ?>
                    <?php if(!empty($log['apiKey'])): ?> <?= htmlspecialchars($log['apiKey']) ?> <br> <?php endif; ?>
                </td>
                <td>
                    <?php foreach ($log['matched_projects'] as $project): ?>
                        <?= $project['site'] ?> <?php if ($project['common_customer']): ?>&copy;<?php endif; ?> <br>
                    <?php endforeach; ?>
                </td>
                <td><?= $log['date'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-align-center pager">
        <?= LinkPager::widget([
            'pagination' => $pagination,
        ]); ?>
    </div>

</div>