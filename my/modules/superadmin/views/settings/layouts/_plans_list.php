<?php
    /* @var $this yii\web\View */
    /* @var $plans \superadmin\models\search\PlanSearch */
    /* @var $plan \common\models\panels\Tariff */

    use my\helpers\Url;
    use yii\bootstrap\Html;
    use my\helpers\PriceHelper;
?>

<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th scope="col"><?= Yii::t('app/superadmin', 'settings.plan.column_name') ?></th>
        <th scope="col"><?= Yii::t('app/superadmin', 'settings.plan.column_rate') ?></th>
        <th scope="col"><?= Yii::t('app/superadmin', 'settings.plan.column_description') ?></th>
        <th class="table-custom__action-th"></th>
    </tr>
    </thead>
    <tbody>
        <?php if ($plans) : ?>
            <?php foreach ($plans as $plan) : ?>
                <tr>
                    <td><?= $plan->title ?></td>
                    <td><?= PriceHelper::prepare($plan->price) ?></td>
                    <td><?= $plan->description ?></td>

                    <td class="text-right">
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'settings.plan.actions_label') ?></button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <?= Html::a(Yii::t('app/superadmin', 'settings.plan.action_edit_plan') , Url::toRoute(['/settings/edit-plan', 'id' => $plan->id]), [
                                    'class' => 'dropdown-item edit-plan',
                                    'data-details' => json_encode($plan->getAttributes())
                                ])?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="8"><?= Yii::t('app/superadmin', 'settings.plan.no_plans')?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>