<?php
/* @var $this yii\web\View */
/* @var $contents \superadmin\models\search\ContentSearch */

use my\helpers\Url;
use yii\bootstrap\Html;

$this->context->addModule('superadminContentController');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group list-group__custom">
                    <a href="<?=Url::toRoute('/settings')?>" class="list-group-item list-group-item-action"><span class="fa fa-credit-card"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_payments')?></a>
                    <a href="<?=Url::toRoute('/settings/staff')?>" class="list-group-item list-group-item-action"><span class="fa fa-user"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_staff')?></a>
                    <a href="<?=Url::toRoute('/settings/email')?>" class="list-group-item list-group-item-action"><span class="fa fa-envelope-o"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_email')?></a>
                    <a href="<?=Url::toRoute('/settings/plan')?>" class="list-group-item list-group-item-action"><span class="fa fa-list-alt"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_plan')?></a>
                    <a href="<?=Url::toRoute('/settings/content')?>" class="list-group-item list-group-item-action active"><span class="fa fa-file-text-o"></span> <?=Yii::t('app/superadmin', 'pages.settings.content')?></a>
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