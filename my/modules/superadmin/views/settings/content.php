<?php
/* @var $this yii\web\View */
/* @var $contents \my\modules\superadmin\models\search\ContentSearch */

use my\helpers\Url;
use yii\bootstrap\Html;

$this->context->addModule('superadminContentController');
?>
    <div class="container mt-3">
        <div class="row">
            <div class="col-lg-2 offset-lg-1">
                <ul class="nav nav-pills flex-column mb-3">
                    <li class="nav-item">
                        <?= Html::a(Yii::t('app/superadmin', 'pages.settings.menu_payments'), Url::toRoute('/settings'), ['class' => 'nav-link'])?>
                    </li>
                    <li class="nav-item">
                        <?= Html::a(Yii::t('app/superadmin', 'pages.settings.menu_staff'), Url::toRoute('/settings/staff'), ['class' => 'nav-link'])?>
                    </li>
                    <li class="nav-item">
                        <?= Html::a(Yii::t('app/superadmin', 'pages.settings.menu_email'), Url::toRoute('/settings/email'), ['class' => 'nav-link'])?>
                    </li>
                    <li class="nav-item">
                        <?= Html::a(Yii::t('app/superadmin', 'pages.settings.menu_plan'), Url::toRoute('/settings/plan'), ['class' => 'nav-link'])?>
                    </li>
                    <li class="nav-item">
                        <?= Html::a(Yii::t('app/superadmin', 'pages.settings.content'), Url::toRoute('/settings/content'), ['class' => 'nav-link bg-faded'])?>
                    </li>
                </ul>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-block">
                        <?= $this->render('layouts/_contents_list', [
                            'contents' => $contents
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->render('layouts/_edit_content_modal'); ?>