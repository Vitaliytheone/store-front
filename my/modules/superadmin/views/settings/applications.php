<?php
/* @var $this yii\web\View */
/* @var $params \superadmin\models\search\ApplicationsSearch */

use my\helpers\Url;

$this->context->addModule('superadminApplicationsController');
?>

    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group list-group__custom">
                    <a href="<?=Url::toRoute('/settings')?>" class="list-group-item list-group-item-action"><span class="fa fa-credit-card"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_payments')?></a>
                    <a href="<?=Url::toRoute('/settings/staff')?>" class="list-group-item list-group-item-action"><span class="fa fa-user"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_staff')?></a>
                    <a href="<?=Url::toRoute('/settings/email')?>" class="list-group-item list-group-item-action"><span class="fa fa-envelope-o"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_email')?></a>
                    <a href="<?=Url::toRoute('/settings/plan')?>" class="list-group-item list-group-item-action"><span class="fa fa-list-alt"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_plan')?></a>
                    <a href="<?=Url::toRoute('/settings/content')?>" class="list-group-item list-group-item-action"><span class="fa fa-file-text-o"></span> <?=Yii::t('app/superadmin', 'pages.settings.content')?></a>
                    <a href="<?=Url::toRoute('/settings/applications')?>" class="list-group-item list-group-item-action active"><span class="fa fa-cogs"></span> <?=Yii::t('app/superadmin', 'pages.settings.applications')?></a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="mb-3"><?=Yii::t('app/superadmin', 'pages.settings.applications')?></div>
                <?= $this->render('layouts/_applications_list', [
                    'params' => $params
                ]) ?>
            </div>
        </div>
    </div>

<?= $this->render('layouts/_edit_applications_modal'); ?>