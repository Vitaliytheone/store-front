<?php
/* @var $filters array*/
/* @var $pages*/
/* @var $action string */
/* @var $countModels int */
/* @var $pageSizes array */
/* @var $pageSize int */

use my\helpers\Url;

?>

<?php if ($pages->getPageCount() > 1) : ?>
    <?= $pages->getOffset() + 1 ?> to <?= $pages->getOffset() +  $countModels ?> of <?= $pages->totalCount ?>
<?php elseif ( $pages->totalCount) : ?>
    <?= $pages->totalCount  ?>
<?php endif;  ?>
<span class="ml-3">
    <?= Yii::t('app/superadmin', 'panels.pagination.show')?>
    <select name="pageSize" class="pagination-select">
       <?php foreach ($pageSizes as $pageSize): ?>
           <option data-action="<?= Url::toRoute(array_merge($filters, [$action, 'pageSize' => $pageSize])) ?>" <?= $filters['pageSize'] == $pageSize ? 'selected' : ''  ?> value="<?= $pageSize  ?>"><?= $pageSize ?> </option>
       <?php endforeach; ?>
    </select>
</span>
