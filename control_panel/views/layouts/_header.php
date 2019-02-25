<?php
    /* @var $this \yii\web\View */
    /* @var $user \common\models\panels\Customers */

    use yii\bootstrap\Html;
    use control_panel\widgets\UnreadMessagesWidget;
    use control_panel\widgets\UnpaidInvoicesWidget;
    use yii\helpers\ArrayHelper;

    $user = Yii::$app->user->getIdentity();
    $activeTab = !empty($this->context->activeTab) ? $this->context->activeTab : null;
?>
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only"><?= Yii::t('app', 'layouts.header.toggle') ?></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href=""><?= Yii::t('app', 'layouts.header.name') ?></a>
    </div>
    <?php if (!Yii::$app->user->isGuest): ?>
        <ul class="nav navbar-top-links navbar-right">
            <li>
                <a href="/logout">
                    <i class="fa fa-sign-out fa-fw"></i> <?= Yii::t('app', 'layouts.header.logout') ?>
                </a>
            </li>
        </ul>
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search">
                    </li>
                    <?php if ($user && $user->can('stores')) : ?>
                    <li>
                        <?= Html::a('<i class="fa fa-shopping-cart fa-fw"></i> ' . Yii::t('app', 'layouts.header.stores') . ' <sup class="beta-label">' . Yii::t('app', 'layouts.header.beta') . '</sup>', '/stores', [
                                'class' => $activeTab == 'stores' ? 'active' : null,
                        ]) ?>
                    </li>
                    <?php endif; ?>
                    <li>
                        <?= Html::a('<i class="fa fa-table fa-fw"></i> ' . Yii::t('app', 'layouts.header.panels'), '/panels', [
                                'class' => $activeTab == 'panels' ? 'active' : null,
                        ]) ?>
                    </li>
                    <?php if ($user && $user->can('child')) : ?>
                        <li>
                            <?= Html::a('<i class="fa fa-cubes fa-fw"></i> ' . Yii::t('app', 'layouts.header.child_panels'), '/childpanels', [
                                    'class' => $activeTab == 'child-panels' ? 'active' : null,
                            ]) ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user->can('gateway')) : ?>
                        <li>
                            <?= Html::a('<i class="fa fa-exchange fa-fw"></i> ' . Yii::t('app', 'layouts.header.gateway'), '/gateways', [
                                'class' => $activeTab == 'gateway' ? 'active' : null,
                            ]) ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($user && $user->can('domains')) : ?>
                    <li>
                        <?= Html::a('<i class="fa fa fa-globe fa-fw"></i> ' . Yii::t('app', 'layouts.header.domains'), '/domains', [
                                'class' => $activeTab == 'domains' ? 'active' : null,
                        ]) ?>
                    </li>
                    <?php endif; ?>
                    <?php if ($user && $user->can('ssl')) : ?>
                    <li>
                        <?= Html::a('<i class="fa fa-certificate fa-fw"></i> ' . Yii::t('app', 'layouts.header.ssl'), '/ssl', [
                                'class' => $activeTab == 'ssl' ? 'active' : null,
                        ]) ?>
                    </li>
                    <?php endif; ?>
                    <li>
                        <?= Html::a('<i class="fa fa-usd fa-fw"></i> ' . Yii::t('app', 'layouts.header.invoices') . ' ' . UnpaidInvoicesWidget::widget(), '/invoices', [
                                'class' => $activeTab == 'invoices' ? 'active' : null,
                        ]) ?>
                    </li>
                    <?php if ($user && $user->can('referral')) : ?>
                        <li>
                            <?= Html::a('<i class="fa fa-percent fa-fw"></i> ' . Yii::t('app', 'layouts.header.referral'), '/referrals', [
                                    'class' => $activeTab == 'referrals' ? 'active' : null,
                            ]) ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <?= Html::a('<i class="fa fa-support fa-fw"></i>  ' . Yii::t('app', 'layouts.header.support') . ' ' . UnreadMessagesWidget::widget(), '/support', [
                                'class' => $activeTab == 'support' ? 'active' : null,
                        ]) ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="fa fa-gear fa-fw"></i> ' . Yii::t('app', 'layouts.header.settings'), '/settings', [
                                'class' => $activeTab == 'settings' ? 'active' : null,
                        ]) ?>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif ?>
</nav>