<?php

    /* @var $this \yii\web\View */
    /* @var $logItems \my\models\search\ActivitySearch */

    use my\widgets\PagesInfoWidget;
    use yii\widgets\LinkPager;
?>
<div class="row">
    <div class="col-md-8">
        <?= LinkPager::widget([
            'pagination' => $logItems['pages'],
        ]); ?>
    </div>
    <div class="col-md-4 report-block__header-actions">
        <span class="pagination-one-to">
            <?= PagesInfoWidget::widget([
                'pagination' => $logItems['pages'],
            ]); ?>
        </span>
    </div>
</div>