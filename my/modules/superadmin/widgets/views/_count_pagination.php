<?php

use my\modules\superadmin\models\search\CustomersSearch;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $params array */
/* @var $content string */
/* @var $pages yii\data\Pagination */
?>
<?= $content ?>
<span class="ml-3">
    <?= Yii::t('app/superadmin', 'customers.pagination.show') ?>
    <select class="pagination-select" onchange="location = this.value;">
        <?php foreach (CustomersSearch::$pageSizeList as $pageSize => $label) : ?>
            '<option value="<?= Url::current(array_merge(['page_size' => $pageSize], $params)) ?>"
                <?= ($pages->pageSize == $pageSize) ? 'selected' : '' ?>>
            <?= $label ?></option>
        <?php endforeach; ?>
    </select>
</span>