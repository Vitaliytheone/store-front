<?php
/* @var $this yii\web\View */
/* @var $plans \superadmin\models\search\PlanSearch */


$this->context->addModule('superadminPlanController');
?>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group list-group__custom">
                <?= $this->render('layouts/_menu', ['plansActive' => 'active']); ?>
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