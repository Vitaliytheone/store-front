<?php
/* @var $this yii\web\View */

/* @var string $paymentsActive */
/* @var string $staffsActive */
/* @var string $mailsActive */
/* @var string $plansActive */
/* @var string $contentsActive */
/* @var string $applicationsActive */

use control_panel\helpers\Url;

?>

<div class="list-group list-group__custom">
    <a href="<?= Url::toRoute('/settings') ?>" class="list-group-item list-group-item-action <?= $paymentsActive ?? '' ?>"><span class="fa fa-credit-card"></span> <?= Yii::t('app/superadmin', 'pages.settings.menu_payments') ?></a>
    <a href="<?= Url::toRoute('/settings/staff') ?>" class="list-group-item list-group-item-action <?= $staffsActive ?? '' ?>"><span class="fa fa-user"></span> <?= Yii::t('app/superadmin', 'pages.settings.menu_staff') ?></a>
    <a href="<?= Url::toRoute('/settings/email') ?>" class="list-group-item list-group-item-action <?= $mailsActive ?? '' ?>"><span class="fa fa-envelope-o"></span> <?= Yii::t('app/superadmin', 'pages.settings.menu_email') ?></a>
    <a href="<?= Url::toRoute('/settings/content') ?>" class="list-group-item list-group-item-action <?= $contentsActive ?? '' ?>"><span class="fa fa-file-text-o"></span> <?= Yii::t('app/superadmin', 'pages.settings.content') ?></a>
    <a href="<?= Url::toRoute('/settings/applications') ?>" class="list-group-item list-group-item-action <?= $applicationsActive ?? '' ?>"><span class="fa fa-cogs"></span> <?= Yii::t('app/superadmin', 'pages.settings.applications') ?></a>
</div>

