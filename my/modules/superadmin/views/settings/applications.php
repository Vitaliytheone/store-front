<?php
/* @var $this yii\web\View */
/* @var $params \superadmin\models\search\ApplicationsSearch */


$this->context->addModule('superadminApplicationsController');
?>

    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <?= $this->render('layouts/_menu', ['applicationsActive' => 'active']); ?>
            </div>
            <div class="col-md-9">
                <?= $this->render('layouts/_applications_list', [
                    'params' => $params
                ]) ?>
            </div>
        </div>
    </div>

<?= $this->render('layouts/_edit_applications_modal'); ?>