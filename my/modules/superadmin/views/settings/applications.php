<?php
/* @var $this yii\web\View */
/* @var $params \superadmin\models\search\ApplicationsSearch */


$this->context->addModule('superadminApplicationsController');
?>

    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <?= $this->render('layouts/_menu'); ?>
            </div>
            <div class="col-md-9">
                <div class="mb-3"><?= Yii::t('app/superadmin', 'pages.settings.applications') ?></div>
                <?= $this->render('layouts/_applications_list', [
                    'params' => $params
                ]) ?>
            </div>
        </div>
    </div>

<?= $this->render('layouts/_edit_applications_modal'); ?>