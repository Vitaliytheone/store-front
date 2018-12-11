<?php

/* @var $this yii\web\View */
/* @var $incidents array */

use my\helpers\SpecialCharsHelper;
use yii\helpers\Html;
use my\helpers\Url;
use yii\widgets\LinkPager;
use superadmin\widgets\CountPagination;
use common\models\panels\Project;

?>

<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'fraud_incidents.list.id') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_incidents.list.panel') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_incidents.list.payment_id') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_incidents.list.fraud_risk') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_incidents.list.fraud_reason') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_incidents.list.balance') ?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_incidents.list.created') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (SpecialCharsHelper::multiPurifier($incidents['models']) as $incident) : ?>
        <tr>
            <?php
                /** @var Project $panel */
                $panel = $incident['panel'];
            ?>
            <td>
                <?= $incident['id'] ?>
            </td>
            <td>
            <?= Html::a($panel->site,
                Url::toRoute([$panel->child_panel == 0 ? '/panels' : '/child-panels', 'id' => $panel->id]))
            ?>
            </td>
            <td class="table-custom__customer-td">
                <?= $incident['payment_id'] ?>
                <a href="<?= Url::toRoute(['/panels/sign-in-as-admin', 'id' => $panel->id, 'redirect' => '/admin/payments?query=' . $incident['payment_id'] . '&search_type=1']); ?>" target="_blank" class="table-custom__customer-button"  data-placement="top" title="">
                    <span class="my-icons my-icons-autorization"></span>
                </a>
            </td>
            <td>
                <?= $incident['fraud_risk'] ?>
            </td>
            <td>
                <?= $incident['fraud_reason'] ?>
            </td>
            <td>
                <?= $incident['balance_added'] ?>
            </td>
            <td>
                <?= $incident['created_at'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="row">
    <div class="col-md-6">
        <nav>
            <ul class="pagination">
                <?= LinkPager::widget(['pagination' => $incidents['pages'],]); ?>
            </ul>
        </nav>
    </div>
    <div class="col-md-6 text-md-right">
        <?= CountPagination::widget([
            'pages' => $incidents['pages'],
            'params' => $filters
        ]) ?>
    </div>
</div>
