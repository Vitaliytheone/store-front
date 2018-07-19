<?php
/* @var $panel array */

use yii\helpers\Html;
use common\models\panels\Project;
use yii\helpers\Json;
use my\helpers\Url;

$loginUrl = Url::toRoute(['/child-panels/sign-in-as-admin', 'id' => $panel['id']]);
?>

<?= Html::a(Yii::t('app/superadmin', 'panels.list.details'), Url::toRoute(['/child-panels/edit', 'id' => $panel['id']]), [
    'class' => 'dropdown-item edit-panels',
    'data-panels' => Json::encode($panel)
])?>
<?= Html::a(Yii::t('app/superadmin', 'panels.list.providers'), Url::toRoute(['/child-panels/edit-providers', 'id' => $panel['id']]), [
    'class' => 'dropdown-item edit-providers',
    'data-providers' => Json::encode($panel['providers'])
])?>
<?= Html::a(Yii::t('app/superadmin', 'panels.list.expiry_date'), Url::toRoute(['/child-panels/edit-expiry', 'id' => $panel['id']]), [
    'class' => 'dropdown-item edit-expiry',
    'data-expired' => $panel['expired_datetime']
])?>
<?= Html::a(Yii::t('app/superadmin', 'panels.list.chage_domain'), Url::toRoute(['/child-panels/change-domain', 'id' => $panel['id']]), [
    'class' => 'dropdown-item change-domain',
    'data-domain' => $panel['site'],
    'data-subdomain' => $panel['subdomain']
])?>
<?php if (Project::STATUS_ACTIVE == $panel['act']) : ?>
    <?= Html::a(Yii::t('app/superadmin', 'panels.list.freeze_panel'), Url::toRoute(['/child-panels/change-status']), [
        'data-params' => [
            'id' => $panel['id'],
            'status' => Project::STATUS_FROZEN,
        ],
        'class' => 'dropdown-item panels-change-status',
        'data-title' => Yii::t('app/superadmin', 'panels.list.freeze')
    ])?>
<?php elseif (Project::STATUS_FROZEN == $panel['act']) : ?>
    <?= Html::a(Yii::t('app/superadmin', 'panels.list.activate_panel'), Url::toRoute(['/child-panels/change-status']), [
        'class' => 'dropdown-item',
        'data-method' => 'POST',
        'data-params' => [
            'id' => $panel['id'],
            'status' => Project::STATUS_ACTIVE,
        ],
    ])?>
<?php elseif (Project::STATUS_TERMINATED == $panel['act']) : ?>
    <?= Html::a(Yii::t('app/superadmin', 'panels.list.restore_panel'), Url::toRoute(['/child-panels/change-status']), [
        'class' => 'dropdown-item',
        'data-method' => 'POST',
        'data-params' => [
            'id' => $panel['id'],
            'status' => Project::STATUS_FROZEN,
        ],
    ])?>
<?php endif; ?>

<?= Html::a(Yii::t('app/superadmin', 'panels.upgrade.header'), Url::toRoute(['/child-panels/upgrade', 'id' => $panel['id']]), [
    'class' => 'dropdown-item upgrade',
    'data-total' => 25
])?>

<?= Html::a(Yii::t('app/superadmin', 'child_panels.list.sign_in_as_admin'), $loginUrl, [
    'class' => 'dropdown-item',
    'target' => '_blank',
])?>
<?php if (Project::STATUS_FROZEN == $panel['act']): ?>
    <?= Html::a(Yii::t('app/superadmin', 'panels.list.terminate'), Url::toRoute(['/child-panels/change-status']), [
        'class' => 'dropdown-item panels-change-status',
        'data-title' => Yii::t('app/superadmin', 'panels.list.terminated'),
        'data-method' => 'POST',
        'data-params' => [
            'id' => $panel['id'],
            'status' => Project::STATUS_TERMINATED,
        ],
    ])?>
<?php endif; ?>