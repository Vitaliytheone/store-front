<?php
/* @var $this yii\web\View */
/* @var $emails \my\modules\superadmin\models\search\NotificationEmailSearch */

use my\helpers\Url;
use yii\bootstrap\Html;

?>
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-2 offset-lg-1">
            <ul class="nav nav-pills flex-column mb-3">
                <li class="nav-item">
                    <?= Html::a('Payments', Url::toRoute('/settings'), ['class' => 'nav-link'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a('Staff', Url::toRoute('/settings/staff'), ['class' => 'nav-link'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a('Email', Url::toRoute('/settings/email'), ['class' => 'nav-link bg-faded'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a('Plan', Url::toRoute('/settings/plan'), ['class' => 'nav-link'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a(Yii::t('app/superadmin', 'pages.settings.content'), Url::toRoute('/settings/content'), ['class' => 'nav-link'])?>
                </li>
            </ul>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-block">
                    <?= $this->render('layouts/_emails_list', [
                        'emails' => $emails
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>