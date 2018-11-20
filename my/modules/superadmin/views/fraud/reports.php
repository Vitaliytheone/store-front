<?php

use my\helpers\Url;

/* @var $this yii\web\View */
/* @var $reports array */
/* @var $filters array */
/* @var $navs array */

?>


    <div class="row">
        <div class="col-md-8">
            <ul class="nav nav-pills mb-3" role="tablist">
                <?php foreach ($navs as $status => $nav) : ?>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap <?= $filters['status'] === (string)$status  ? 'active' : '' ?>" href="<?= Url::toRoute(['/fraud/reports', 'status' => $status]) ?>">
                            <?= $nav ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?= $this->render('layouts/reports/_reports_list', [
        'reports' => $reports
    ])?>


