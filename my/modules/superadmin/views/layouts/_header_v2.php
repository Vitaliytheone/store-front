<?php
/* @var $this \yii\web\View */
/* @var $content string */

use my\modules\superadmin\widgets\SuperAdminNav;
use my\modules\superadmin\widgets\SuperAdminNavBar;
use my\modules\superadmin\widgets\UnreadMessagesWidgetV2;
use my\modules\superadmin\widgets\ErrorOrdersWidgetV2;
use common\models\panels\SuperAdmin;
use my\helpers\Url;
?>

<?php
SuperAdminNavBar::begin([
    'brandLabel' => Yii::$app->name,
    'brandUrl' => Yii::$app->homeUrl,
    'toggleOptions' => [
        'class' => 'navbar-toggler',
        'type' => 'button',
        'data-toggle' => 'collapse',
        'data-target' => '#navbarsExample09',
        'aria-controls' =>'navbarsExample09',
        'aria-expanded' => 'false',
        'aria-label' => 'Toggle navigation'
    ],
    'containerOptions' => ['class' => 'collapse navbar-collapse', 'id' => 'navbarsExample09'],
    'options' => [
        'class' => 'navbar navbar-expand-lg navbar-light bg-light fixed-top',
    ]
]);

$optionsLeft = [];

$optionsLeft[] = ['label' => Yii::t('app/superadmin',
    'header.nav.dashboard'),
    'url' => [Url::toRoute('/dashboard')],
    'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']
];


if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PANELS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.panels'), 'url' => [Url::toRoute('/panels')], 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}


$serviceItems = [];
if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PANELS)) {
    $serviceItems[] = ['label' => Yii::t('app/superadmin', 'header.nav.child_panels'), 'url' => Url::toRoute('/child-panels'), 'linkOptions' => ['class' => 'dropdown-item']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_DOMAINS)) {
    $serviceItems[] = ['label' => Yii::t('app/superadmin', 'header.nav.domains'), 'url' => Url::toRoute('/domains'), 'linkOptions' => ['class' => 'dropdown-item']];
}
if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_SSL)) {
    $serviceItems[] = ['label' => Yii::t('app/superadmin', 'header.nav.ssl'), 'url' => Url::toRoute('/ssl'), 'linkOptions' => ['class' => 'dropdown-item']];
}

if (count($serviceItems) > 0) {
    $optionsLeft[] = [
        'label' => 'Services',
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'items' => $serviceItems,
    ];
}

$optionsLeft[] = ['label' => 'Subscriptions', 'url' => ['#'], 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_ORDERS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.orders') . ' ' . ErrorOrdersWidgetV2::widget(),
        'url' => Url::toRoute('/orders'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link']
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_CUSTOMERS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.customers'), 'url' => Url::toRoute('/customers'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_REFERRALS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.referrals'), 'url' => Url::toRoute('/referrals'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_INVOICES)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.invoices'),'url' => Url::toRoute('/invoices'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PAYMENTS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.payments'), 'url' => Url::toRoute('/payments'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_TICKETS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.tickets') . ' ' . UnreadMessagesWidgetV2::widget(),
        'url' => Url::toRoute('/tickets'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link']
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PROVIDERS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.providers'), 'url' => Url::toRoute('/providers'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_REPORTS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.reports'), 'url' => Url::toRoute('/reports'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_LOGS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.logs'), 'url' => Url::toRoute('/logs'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_TOOLS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.tools'), 'url' => Url::toRoute('/tools'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_SETTINGS)) {
    $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.settings'), 'url' => Url::toRoute('/settings'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']];
}

echo SuperAdminNav::widget([
    'options' => ['class' => 'navbar-nav mr-auto'],
    'encodeLabels' => false,
    'items' => $optionsLeft,
]);

echo SuperAdminNav::widget([
    'options' => ['class' => ' navbar-nav'],
    'encodeLabels' => false,
    'items' => [
        ['label' => Yii::$app->superadmin->getIdentity()->getFullName(), 'url' => Url::toRoute('/account'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']],
        ['label' => Yii::t('app/superadmin', 'header.nav.logout'), 'url' => Url::toRoute('/logout'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']],
    ]
]);

SuperAdminNavBar::end();
?>
