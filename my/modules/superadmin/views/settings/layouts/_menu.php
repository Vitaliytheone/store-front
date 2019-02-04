<?php
/* @var $this yii\web\View */

/* @var $params \superadmin\models\search\ApplicationsSearch */

use my\helpers\Url;

/**
 * Set active class
 * @param string $link
 * @return string
 */
function setActive($link)
{
    if (strpos(Url::current(), $link) !== false){
        return 'active';
    }
    return '';
}

?>

<div class="list-group list-group__custom">
    <a href="<?= Url::toRoute('/settings') ?>" class="list-group-item list-group-item-action <?= setActive('index') ?>"><span class="fa fa-credit-card"></span> <?= Yii::t('app/superadmin', 'pages.settings.menu_payments') ?></a>
    <a href="<?= Url::toRoute('/settings/staff') ?>" class="list-group-item list-group-item-action <?= setActive('staff') ?>"><span class="fa fa-user"></span> <?= Yii::t('app/superadmin', 'pages.settings.menu_staff') ?></a>
    <a href="<?= Url::toRoute('/settings/email') ?>" class="list-group-item list-group-item-action <?= setActive('email') ?>"><span class="fa fa-envelope-o"></span> <?= Yii::t('app/superadmin', 'pages.settings.menu_email') ?></a>
    <a href="<?= Url::toRoute('/settings/plan') ?>" class="list-group-item list-group-item-action <?= setActive('plan') ?>"><span class="fa fa-list-alt"></span> <?= Yii::t('app/superadmin', 'pages.settings.menu_plan') ?></a>
    <a href="<?= Url::toRoute('/settings/content') ?>" class="list-group-item list-group-item-action <?= setActive('content') ?>"><span class="fa fa-file-text-o"></span> <?= Yii::t('app/superadmin', 'pages.settings.content') ?></a>
    <a href="<?= Url::toRoute('/settings/applications') ?>" class="list-group-item list-group-item-action <?= setActive('applications') ?>"><span class="fa fa-cogs"></span> <?= Yii::t('app/superadmin', 'pages.settings.applications') ?></a>
</div>

