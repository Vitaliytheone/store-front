<?php

/* @var $this yii\web\View */
/* @var $incidents array */

use my\helpers\SpecialCharsHelper;
use yii\helpers\Html;
use my\helpers\Url;
use yii\widgets\LinkPager;
use superadmin\widgets\CountPagination;

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
            <td>
                <?= $incident['id'] ?>
            </td>
            <td>
            <?= Html::a($incident['panel_domain'],
                Url::toRoute([$incident['is_child'] == 0 ? '/panels' : '/child-panels', 'id' => $incident['panel_id']]))
            ?>
            </td>
            <td>
                <?= $incident['payment_id'] ?>
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
