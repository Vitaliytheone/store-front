<?php
    /* @var $this yii\web\View */
    /* @var $plans \my\modules\superadmin\models\search\PlanSearch */

    use my\helpers\Url;
    use yii\bootstrap\Html;

    $this->context->addModule('superadminPlanController');
?>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group list-group__custom">
                <a href="<?=Url::toRoute('/settings')?>" class="list-group-item list-group-item-action"><span class="fa fa-credit-card"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_payments')?></a>
                <a href="<?=Url::toRoute('/settings/staff')?>" class="list-group-item list-group-item-action"><span class="fa fa-user"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_staff')?></a>
                <a href="<?=Url::toRoute('/settings/email')?>" class="list-group-item list-group-item-action"><span class="fa fa-envelope-o"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_email')?></a>
                <a href="<?=Url::toRoute('/settings/plan')?>" class="list-group-item list-group-item-action active"><span class="fa fa-list-alt"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_plan')?></a>
                <a href="<?=Url::toRoute('/settings/content')?>" class="list-group-item list-group-item-action"><span class="fa fa-file-text-o"></span> <?=Yii::t('app/superadmin', 'pages.settings.content')?></a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="mb-3">
                <button type="button" class="btn btn-light" id="createPlan"><?= Yii::t('app/superadmin', 'settings.plan.add_plan_btn') ?></button>
            </div>
            <?= $this->render('layouts/_plans_list', [
                    'plans' => $plans
            ]) ?>
        </div>
    </div>
</div>

<?= $this->render('layouts/_create_plan_modal'); ?>
<?= $this->render('layouts/_edit_plan_modal'); ?>