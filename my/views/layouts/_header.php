<?php
    /* @var $this \yii\web\View */
    /* @var $user \common\models\panels\Customers */

    use yii\bootstrap\Html;
    use my\widgets\UnreadMessagesWidget;

    $user = Yii::$app->user->getIdentity();
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
                        <?= Html::a('<i class="fa fa-shopping-cart fa-fw"></i> ' . Yii::t('app', 'layouts.header.stores'), '/stores') ?>
                    </li>
                    <?php endif; ?>
                    <li>
                        <?= Html::a('<i class="fa fa-table fa-fw"></i> ' . Yii::t('app', 'layouts.header.panels'), '/panels') ?>
                    </li>
                    <?php if ($user && $user->can('child')) : ?>
                        <li>
                            <?= Html::a('<i class="fa fa-cubes fa-fw"></i> ' . Yii::t('app', 'layouts.header.child_panels'), '/childpanels') ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <?= Html::a('<i class="fa fa fa-globe fa-fw"></i> ' . Yii::t('app', 'layouts.header.domains'), '/domains') ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="fa fa-certificate fa-fw"></i> ' . Yii::t('app', 'layouts.header.ssl'), '/ssl') ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="fa fa-usd fa-fw"></i> ' . Yii::t('app', 'layouts.header.invoices'), '/invoices') ?>
                    </li>
                    <?php if ($user && $user->can('referral')) : ?>
                        <li>
                            <?= Html::a('<i class="fa fa-percent fa-fw"></i> ' . Yii::t('app', 'layouts.header.referral'), '/referrals') ?>
                        </li>
                    <?php endif; ?>
                    <li>
                        <?= Html::a('<i class="fa fa-support fa-fw"></i>  ' . Yii::t('app', 'layouts.header.support') . ' ' .UnreadMessagesWidget::widget(), '/support') ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="fa fa-gear fa-fw"></i> ' . Yii::t('app', 'layouts.header.settings'), '/settings') ?>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif ?>
</nav>