<?php

use my\modules\superadmin\models\search\CustomersSearch;
use my\helpers\Url;

/* @var $this yii\web\View */
/* @var $params array */
/* @var $content string */
/* @var $url string */
/* @var $pages yii\data\Pagination */

$url =  trim(Yii::$app->controller->getRoute(), 'superadmin');
?>
<?= $content ?>
<span class="ml-3">
    <?= Yii::t('app/superadmin', 'customers.pagination.show') ?>
    <select class="pagination-select" onchange="location = this.value;">
        <?php foreach (CustomersSearch::$pageSizeList as $pageSize => $label) : ?>
            '<option value="<?= Url::to(array_merge([$url, 'page_size' => $pageSize], $params)) ?>"
                <?= ($pages->pageSize == $pageSize) ? 'selected' : '' ?>>
            <?= $label ?></option>
        <?php endforeach; ?>
    </select>
</span>