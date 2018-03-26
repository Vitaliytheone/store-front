<?php
    /* @var $this yii\web\View */
    /* @var $plans \my\modules\superadmin\models\search\PlanSearch */

    use my\helpers\Url;
    use yii\bootstrap\Html;

    $this->context->addModule('superadminPlanController');
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
                    <?= Html::a(Yii::t('app/superadmin', 'pages.settings.menu_plan'), Url::toRoute('/settings/plan'), ['class' => 'nav-link bg-faded'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a(Yii::t('app/superadmin', 'pages.settings.content'), Url::toRoute('/settings/content'), ['class' => 'nav-link'])?>
                </li>
            </ul>
        </div>
        <div class="col-lg-9">
            <button type="button" class="btn btn-secondary mb-3" id="createPlan"><?= Yii::t('app/superadmin', 'settings.plan.add_plan_btn') ?></button>
            <div class="card">
                <div class="card-block">
                    <?= $this->render('layouts/_plans_list', [
                        'plans' => $plans
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('layouts/_create_plan_modal'); ?>
<?= $this->render('layouts/_edit_plan_modal'); ?>