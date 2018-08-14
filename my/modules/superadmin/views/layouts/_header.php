<?php
    /* @var $this yii\web\View */

    use yii\bootstrap\Html;
    use yii\bootstrap\Nav;
    use common\models\panels\SuperAdmin;
    use yii\helpers\ArrayHelper;
    use my\modules\superadmin\widgets\ErrorOrdersWidget;
    use my\helpers\Url;
    use my\modules\superadmin\widgets\UnreadMessagesWidget;
    use my\modules\superadmin\widgets\ErrorSslWidget;

    $activeTab = empty($this->context->activeTab) ? null : ArrayHelper::getValue($this->context, 'activeTab');

    $defaultOptions = [
        'options'=> [
            'class'=> 'nav-item'
        ], 'linkOptions' => [
            'class' => 'nav-link'
        ]
    ];

    $optionsLeft = [];

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PANELS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.panels'), 'url' => Url::toRoute('/panels'), 'active' => 'panels' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PANELS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.child_panels'), 'url' => Url::toRoute('/child-panels'), 'active' => 'child-panels' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PANELS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.stores'), 'url' => Url::toRoute('/stores'), 'active' => 'stores' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_ORDERS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.orders') . ' ' . ErrorOrdersWidget::widget(), 'url' => Url::toRoute('/orders'), 'active' => 'orders' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_DOMAINS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.domains'), 'url' => Url::toRoute('/domains'), 'active' => 'domains' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_SSL)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.ssl') . ' ' . ErrorSslWidget::widget(), 'url' => Url::toRoute('/ssl'), 'active' => 'ssl' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_CUSTOMERS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.customers'), 'url' => Url::toRoute('/customers'), 'active' => 'customers' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_REFERRALS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.referrals'), 'url' => Url::toRoute('/referrals'), 'active' => 'referrals' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_INVOICES)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.invoices'), 'url' => Url::toRoute('/invoices'), 'active' => 'invoices' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PAYMENTS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.payments'), 'url' => Url::toRoute('/payments'), 'active' => 'payments' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_TICKETS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.tickets') . ' ' . UnreadMessagesWidget::widget(), 'url' => Url::toRoute('/tickets'), 'active' => 'tickets' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PROVIDERS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.providers'), 'url' => Url::toRoute('/providers'), 'active' => 'providers' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_REPORTS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.reports'), 'url' => Url::toRoute('/reports'), 'active' => 'reports' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_STATUSES)) {
        $optionsLeft[] = [
            'label' => Yii::t('app/superadmin', 'header.nav.statuses'),
            'active' => 'statuses' === $activeTab,
            'items' => [
                ['label' => Yii::t('app/superadmin', 'header.nav.getstatus'), 'url' => Url::toRoute('statuses/getstatus')],
                ['label' => Yii::t('app/superadmin', 'header.nav.subscription'), 'url' => Url::toRoute('statuses/subscription')],
                ['label' => Yii::t('app/superadmin', 'header.nav.sender'), 'url' => Url::toRoute('statuses/sender')],
            ]
        ];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_LOGS)) {
        $optionsLeft[] = [
            'label' => Yii::t('app/superadmin', 'header.nav.logs'),
            'active' => 'logs' === $activeTab,
            'items' => [
                ['label' => Yii::t('app/superadmin', 'header.nav.status_log'), 'url' => Url::toRoute('/logs/status')],
                ['label' => Yii::t('app/superadmin', 'header.nav.providers_log'), 'url' => Url::toRoute('/logs/providers')],
                ['label' => Yii::t('app/superadmin', 'header.nav.api_keys_log'), 'url' => Url::toRoute('/logs/api-keys')],
                ['label' => Yii::t('app/superadmin', 'header.nav.credits'), 'url' => Url::toRoute('/logs/credits')],
            ]
        ];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_TOOLS)) {
        $optionsLeft[] = [
            'label' => Yii::t('app/superadmin', 'header.nav.tools'),
            'active' => 'tools' === $activeTab,
            'items' => [
                ['label' => Yii::t('app/superadmin', 'header.nav.levopanel_scanner'), 'url' => Url::toRoute('/tools/levopanel')],
                ['label' => Yii::t('app/superadmin', 'header.nav.panelfire_scanner'), 'url' => Url::toRoute('/tools/panelfire')],
                ['label' => Yii::t('app/superadmin', 'header.nav.rentalpanel_scanner'), 'linkOptions' => ['class' => 'dropdown-item'], 'url' => Url::toRoute('/tools/rentalpanel')]
            ]
        ];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_SETTINGS)) {
        $optionsLeft[] = ['label' => Yii::t('app/superadmin', 'header.nav.settings'), 'url' => Url::toRoute('/settings'), 'active' => 'settings' == $activeTab];
    }

    $optionsRight = [
        ['label' => Yii::t('app/superadmin', 'header.nav.account'), 'url' => Url::toRoute('/account'), 'active' => 'account' == $activeTab],
        ['label' => Yii::t('app/superadmin', 'header.nav.logout'), 'url' => Url::toRoute('/logout'), 'active' => 'logout' == $activeTab],
    ];

    foreach ($optionsLeft as &$option) {
        $option = array_merge($option, $defaultOptions);
    }

    foreach ($optionsRight as &$option) {
        $option = array_merge($option, $defaultOptions);
    }
?>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <?= Html::a('Perfect Panel', Url::toRoute('/'), ['class' => 'navbar-brand'])?>

    <div class="collapse navbar-collapse" id="navbarContent">
        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav mr-auto'],
            'items' => $optionsLeft,
            'encodeLabels' => false,
            'dropdownClass' => 'my\modules\superadmin\widgets\CustomDropdown',
        ]);?>

        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => $optionsRight,
        ]);?>
    </div>
</nav>