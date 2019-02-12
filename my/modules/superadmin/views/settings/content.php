<?php
/* @var $this yii\web\View */
/* @var $contents \superadmin\models\search\ContentSearch */


$this->context->addModule('superadminContentController');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group list-group__custom">
                    <?= $this->render('layouts/_menu', ['contentsActive' => 'active']); ?>
                </div>
            </div>
            <div class="col-md-9">
                <?= $this->render('layouts/_contents_list', [
                    'contents' => $contents
                ]) ?>
            </div>
        </div>
    </div>

<?= $this->render('layouts/_edit_content_modal'); ?>