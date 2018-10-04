<?php
/* @var $this \yii\web\View */
/* @var $content string */

use my\modules\superadmin\widgets\SuperAdminNav;
use my\modules\superadmin\widgets\SuperAdminNavBar;
use my\modules\superadmin\widgets\UnreadMessagesWidgetV2;
use my\modules\superadmin\widgets\ErrorOrdersWidgetV2;
use common\models\panels\SuperAdmin;
use my\helpers\Url;
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
        'label' => Yii::t('app/superadmin', 'header.nav.panels'),
        'url' => [Url::toRoute('/panels')],
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'panels' == $activeTab
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PANELS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.child_panels'),
        'url' => Url::toRoute('/child-panels'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'child-panels' == $activeTab
    ];
}

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
        'label' => Yii::t('app/superadmin', 'header.nav.ssl'),
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

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_REFERRALS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.referrals'),
        'url' => Url::toRoute('/referrals'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'referrals' == $activeTab
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

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PROVIDERS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.providers'),
        'url' => Url::toRoute('/providers'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'providers' == $activeTab
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

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_STATUSES)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.statuses'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'statuses' === $activeTab,
        'items' => [
            [
                'label' => Yii::t('app/superadmin', 'header.nav.getstatus'),
                'url' => Url::toRoute('/statuses/getstatus'),
                'linkOptions' => ['class' => 'dropdown-item'],
            ],
            [
                'label' => Yii::t('app/superadmin', 'header.nav.subscription'),
                'url' => Url::toRoute('/statuses/subscription'),
                'linkOptions' => ['class' => 'dropdown-item'],
            ],
            [
                'label' => Yii::t('app/superadmin', 'header.nav.sender'),
                'url' => Url::toRoute('/statuses/sender'),
                'linkOptions' => ['class' => 'dropdown-item'],
            ],
        ]
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_LOGS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.logs'),
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'active' => 'logs' === $activeTab,
        'items' => [
            [
                'label' => Yii::t('app/superadmin', 'header.nav.status_log'),
                'url' => Url::toRoute('/logs/status'),
                'linkOptions' => ['class' => 'dropdown-item'],
            ],
            [
                'label' => Yii::t('app/superadmin', 'header.nav.providers_log'),
                'url' => Url::toRoute('/logs/providers'),
                'linkOptions' => ['class' => 'dropdown-item']],
            [
                'label' => Yii::t('app/superadmin', 'header.nav.api_keys_log'),
                'url' => Url::toRoute('/logs/api-keys'),
                'linkOptions' => ['class' => 'dropdown-item']
            ],
            [
                'label' => Yii::t('app/superadmin', 'header.nav.credits'),
                'url' => Url::toRoute('/logs/credits'),
                'linkOptions' => ['class' => 'dropdown-item']
            ],
        ]
    ];
}

if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_TOOLS)) {
    $optionsLeft[] = [
        'label' => Yii::t('app/superadmin', 'header.nav.tools'),
        'active' => 'tools' === $activeTab,
        'options' => ['class' => 'nav-item'],
        'linkOptions' => ['class' => 'nav-link'],
        'items' => [
            ['label' => Yii::t('app/superadmin', 'header.nav.levopanel_scanner'), 'linkOptions' => ['class' => 'dropdown-item'], 'url' => Url::toRoute('/tools/levopanel')],
            ['label' => Yii::t('app/superadmin', 'header.nav.panelfire_scanner'), 'linkOptions' => ['class' => 'dropdown-item'], 'url' => Url::toRoute('/tools/panelfire')],
            ['label' => Yii::t('app/superadmin', 'header.nav.rentalpanel_scanner'), 'linkOptions' => ['class' => 'dropdown-item'], 'url' => Url::toRoute('/tools/rentalpanel')],
            ['label' => Yii::t('app/superadmin', 'header.nav.db_helper'), 'linkOptions' => ['class' => 'dropdown-item'], 'url' => Url::toRoute('/tools/db-helper')],
            ['label' => Yii::t('app/superadmin', 'header.nav.fraud_reports'), 'linkOptions' => ['class' => 'dropdown-item'], 'url' => Url::toRoute('/tools/fraud-reports')],
        ]
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
