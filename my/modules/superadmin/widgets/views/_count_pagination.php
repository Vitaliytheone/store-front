<?php

use my\modules\superadmin\widgets\CountPagination;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $params array */
/* @var $content string */
/* @var $pages yii\data\Pagination */

$pageSizeList = CountPagination::$pageSizeList;
$pageSizeList['all'] = Yii::t('app/superadmin', 'customers.pagination.all');
?>
<?= $content ?>
<span class="ml-3">
    <?= Yii::t('app/superadmin', 'customers.pagination.show') ?>
    <select class="pagination-select" onchange="location = this.value;">
        <?php foreach ($pageSizeList as $pageSize => $label) : ?>
            '<option value="<?= Url::current(array_merge(['page_size' => $pageSize], $params)) ?>"
                <?= ($pages->pageSize == $pageSize) ? 'selected' : '' ?>>
            <?= $label ?></option>
        <?php endforeach; ?>
    </select>
</span>