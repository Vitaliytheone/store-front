<?php
/* @var $this \yii\web\View */
/* @var $content string */

use superadmin\widgets\SuperAdminNav;
use superadmin\widgets\SuperAdminNavBar;
use superadmin\widgets\UnreadMessagesWidgetV2;
use superadmin\widgets\ErrorOrdersWidgetV2;
use superadmin\widgets\ErrorSslWidget;
use common\models\panels\SuperAdmin;
use control_panel\helpers\Url;
use yii\helpers\ArrayHelper;

$activeTab = empty($this->context->activeTab) ? null : ArrayHelper::getValue($this->context, 'activeTab');

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
    'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link'],
    'active' => 'dashboard' == $activeTab
];

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PANELS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.stores'),
        'url' => Url::toRoute('/stores'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'stores' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_DOMAINS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.domains'),
        'url' => Url::toRoute('/domains'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'domains' == $activeTab
    ];

}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_SSL)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.ssl') . ' ' . ErrorSslWidget::widget(),
        'url' => Url::toRoute('/ssl'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'ssl' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_ORDERS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.orders') . ' ' . ErrorOrdersWidgetV2::widget(),
        'url' => Url::toRoute('/orders'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'orders' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_CUSTOMERS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.customers'),
        'url' => Url::toRoute('/customers'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'customers' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_INVOICES)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.invoices'),
        'url' => Url::toRoute('/invoices'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'invoices' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PAYMENTS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.payments'),
        'url' => Url::toRoute('/payments'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'payments' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_TICKETS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.tickets') . ' ' . UnreadMessagesWidgetV2::widget(),
        'url' => Url::toRoute('/tickets'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'tickets' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_REPORTS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.reports'),
        'url' => Url::toRoute('/reports'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'reports' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_SETTINGS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.settings'),
        'url' => Url::toRoute('/settings'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'settings' === $activeTab
    ];
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
        ['label' => Yii::t('app/superadmin', 'header.nav.account'), 'url' => Url::toRoute('/account'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']],
        ['label' => Yii::t('app/superadmin', 'header.nav.logout'), 'url' => Url::toRoute('/logout'), 'options' => ['class' => 'nav-item'], 'linkOptions' => ['class' => 'nav-link']],
    ]
]);

SuperAdminNavBar::end();
?>
