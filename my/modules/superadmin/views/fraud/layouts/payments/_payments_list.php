<?php
/* @var $this yii\web\View */
/* @var $payments array */
/* @var $panel Project */

use my\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use common\models\panels\Project;
use my\helpers\SpecialCharsHelper;
use superadmin\widgets\CountPagination;

?>
<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.id')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.panel')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.payment_id')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.payer_id')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.email')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.firstname')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.lastname')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.created')?></th>
        <th><?= Yii::t('app/superadmin', 'fraud_payments.list.updated')?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach (SpecialCharsHelper::multiPurifier($payments['models']) as $key => $payment) : ?>
            <tr>
                <td>
                    <?= $payment['id'] ?>
                </td>
                <td>
                    <?php $panel = $payment['panel']; ?>
                    <?= Html::a($panel->site, Url::toRoute([$panel->child_panel === 0 ? '/panels' : '/child-panels', 'id' => $panel->id]))?>
                </td>
                <td>
                    <?= $payment['payment_id'] ?>
                </td>
                <td>
                    <?= $payment['payer_id'] ?>
                </td>
                <td>
                    <?= $payment['payer_email'] ?>
                </td>
                <td>
                    <?= $payment['firstname'] ?>
                </td>
                <td>
                    <?= $payment['lastname'] ?>
                </td>
                <td>
                    <?= $payment['created_at'] ?>
                </td>
                <td>
                    <?= $payment['updated_at'] ?>
                </td>
            </tr>
        <?php endforeach; ?>

    </tbody>
</table>

<div class="row">
    <div class="col-md-6">
        <nav>
            <ul class="pagination">
                <?= LinkPager::widget(['pagination' => $payments['pages'],]); ?>
            </ul>
        </nav>
    </div>
    <div class="col-md-6 text-md-right">
        <?= CountPagination::widget([
            'pages' => $payments['pages'],
            'params' => $filters
        ]) ?>
    </div>
</div>