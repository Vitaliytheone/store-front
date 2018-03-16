<?php
    /* @var $this yii\web\View */

    use yii\bootstrap\Html;
    use yii\bootstrap\Nav;
    use common\models\panels\SuperAdmin;
    use yii\helpers\ArrayHelper;
    use my\modules\superadmin\widgets\ErrorOrdersWidget;
    use my\helpers\Url;
    use my\modules\superadmin\widgets\UnreadMessagesWidget;

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
        $optionsLeft[] = ['label' => 'Panels', 'url' => Url::toRoute('/panels'), 'active' => 'panels' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PANELS)) {
        $optionsLeft[] = ['label' => 'Child Panels', 'url' => Url::toRoute('/child-panels'), 'active' => 'child-panels' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_ORDERS)) {
        $optionsLeft[] = ['label' => 'Orders ' . ErrorOrdersWidget::widget(), 'url' => Url::toRoute('/orders'), 'active' => 'orders' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_DOMAINS)) {
        $optionsLeft[] = ['label' => 'Domains', 'url' => Url::toRoute('/domains'), 'active' => 'domains' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_SSL)) {
        $optionsLeft[] = ['label' => 'SSL', 'url' => Url::toRoute('/ssl'), 'active' => 'ssl' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_CUSTOMERS)) {
        $optionsLeft[] = ['label' => 'Customers', 'url' => Url::toRoute('/customers'), 'active' => 'customers' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_REFERRALS)) {
        $optionsLeft[] = ['label' => 'Referrals', 'url' => Url::toRoute('/referrals'), 'active' => 'referrals' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_INVOICES)) {
        $optionsLeft[] = ['label' => 'Invoices', 'url' => Url::toRoute('/invoices'), 'active' => 'invoices' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PAYMENTS)) {
        $optionsLeft[] = ['label' => 'Payments', 'url' => Url::toRoute('/payments'), 'active' => 'payments' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_TICKETS)) {
        $optionsLeft[] = ['label' => 'Tickets ' . UnreadMessagesWidget::widget(), 'url' => Url::toRoute('/tickets'), 'active' => 'tickets' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_PROVIDERS)) {
        $optionsLeft[] = ['label' => 'Providers', 'url' => Url::toRoute('/providers'), 'active' => 'providers' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_REPORTS)) {
        $optionsLeft[] = ['label' => 'Reports', 'url' => Url::toRoute('/reports'), 'active' => 'reports' == $activeTab];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_LOGS)) {
        $optionsLeft[] = [
            'label' => 'Logs',
            'active' => 'logs' === $activeTab,
            'items' => [
                ['label' => 'Status log', 'url' => Url::toRoute('/logs/status')],
                ['label' => 'Providers log', 'url' => Url::toRoute('/logs/providers')],
                ['label' => 'API keys log', 'url' => Url::toRoute('/logs/api-keys')],
                ['label' => 'Credits', 'url' => Url::toRoute('/logs/credits')],
            ]
        ];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_TOOLS)) {
        $optionsLeft[] = [
            'label' => 'Tools',
            'active' => 'tools' === $activeTab,
            'items' => [
                ['label' => 'Levopanel scanner', 'url' => Url::toRoute('/tools/levopanel')],
                ['label' => 'Panelfire scanner', 'url' => Url::toRoute('/tools/panelfire')],
            ]
        ];
    }

    if (Yii::$app->superadmin->can(SuperAdmin::CAN_WORK_WITH_SETTINGS)) {
        $optionsLeft[] = ['label' => 'Settings', 'url' => Url::toRoute('/settings'), 'active' => 'settings' == $activeTab];
    }

    $optionsRight = [
        ['label' => 'Account', 'url' => Url::toRoute('/account'), 'active' => 'account' == $activeTab],
        ['label' => 'Logout', 'url' => Url::toRoute('/logout'), 'active' => 'logout' == $activeTab],
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
        <?php /*<ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="'" href="panels.html">Panels</a></li>
            <li class="nav-item"><a class="nav-link" href="domains.html">Domains</a></li>
            <li class="nav-item"><a class="nav-link" href="ssl.html">SSL</a></li>
            <li class="nav-item"><a class="nav-link" href="customers.html">Customers</a></li>
            <li class="nav-item"><a class="nav-link" href="invoices.html">Invoices</a></li>
            <li class="nav-item"><a class="nav-link" href="payments.html">Payments</a></li>
            <li class="nav-item"><a class="nav-link" href="tickets.html">Tickets</a></li>
            <li class="nav-item"><a class="nav-link" href="providers.html">Providers</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.html">Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="logs.html">Logs</a></li>
            <li class="nav-item"><a class="nav-link" href="settings.html">Settings</a></li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item active"><a class="nav-link" href="account.html">Account</a></li>
            <li class="nav-item"><a class="nav-link" href="">Logout</a></li>
        </ul> */ ?>
    </div>
</nav>