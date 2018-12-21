<?php
/* @var $this yii\web\View */
/* @var $gateways array */
/* @var $gateway array */
/* @var $filters array */
/* @var $action string */
/* @var $pageSize */

use my\helpers\Url;
use yii\widgets\LinkPager;
use superadmin\widgets\CountPagination;
use my\helpers\SpecialCharsHelper;
use yii\helpers\Html;
use common\models\gateways\Sites;

$now = time();
?>
<div class="tab-pane fade show active" id="status-all" role="tabpanel">
    <table class="table table-sm table-custom">
        <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'gateways.list.id')?></th>
            <th><?= Yii::t('app/superadmin', 'gateways.list.domain')?></th>
            <th><?= Yii::t('app/superadmin', 'gateways.list.customer')?></th>
            <th><?= Yii::t('app/superadmin', 'gateways.list.status')?></th>
            <th><?= Yii::t('app/superadmin', 'gateways.list.created')?></th>
            <th><?= Yii::t('app/superadmin', 'gateways.list.expiry')?></th>
            <th class="table-custom__action-th w-1"></th>
        </tr>
        </thead>
        <tbody>
            <?php foreach (SpecialCharsHelper::multiPurifier($gateways['models']) as $key => $gateway) : ?>
                <tr>
                    <td>
                        <?= $gateway['id'] ?>
                    </td>
                    <td class="table-no-wrap table-custom__customer-td">
                        <?= $gateway['domain'] ?>
                        <a href="<?= Url::toRoute(['/gateways/sign-in-as-admin', 'id' => $gateway['id']]) ?>" target="_blank" class="table-custom__customer-button"  data-placement="top" title="">
                            <span class="my-icons my-icons-autorization"></span>
                        </a>
                    </td>
                    <td>
                        <?= Html::a($gateway['customer_email'], Url::toRoute(['/customers', 'id' => $gateway['customer_id']])) ?>
                    </td>
                    <td>
                        <?= $gateway['status_name'] ?>
                    </td>
                    <td>
                        <?= $gateway['created'] ?>
                    </td>
                    <td>
                        <?= $gateway['expiry'] ?>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= Yii::t('app/superadmin', 'gateways.list.actions')?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <?= Html::a(Yii::t('app/superadmin', 'gateways.list.action.edit_expiry'), Url::toRoute(['/gateways/edit-expiry', 'id' => $gateway['id']]), [
                                    'class' => 'dropdown-item edit-expiry',
                                    'data-expired' => htmlspecialchars_decode($gateway['expiry'])
                                ])?>

                                <?= Html::a(Yii::t('app/superadmin', 'gateways.list.action.change_domain'), Url::toRoute(['/gateways/change-domain', 'id' => $gateway['id']]), [
                                    'class' => 'dropdown-item change-domain',
                                    'data-domain' => htmlspecialchars_decode($gateway['domain']),
                                    'data-subdomain' => $gateway['subdomain']
                                ])?>

                                <?php if (Sites::STATUS_ACTIVE == $gateway['status']) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'gateways.list.action.freeze'), Url::toRoute(['/gateways/change-status']), [
                                        'data-params' => [
                                            'id' => $gateway['id'],
                                            'status' => Sites::STATUS_FROZEN,
                                        ],
                                        'class' => 'dropdown-item gateway-change-status',
                                        'data-title' => Yii::t('app/superadmin', 'gateways.list.freeze')
                                    ])?>
                                <?php elseif (Sites::STATUS_FROZEN == $gateway['status']) : ?>
                                    <?= Html::a(Yii::t('app/superadmin', 'gateways.list.action.activate'), Url::toRoute(['/gateways/change-status']), [
                                        'class' => 'dropdown-item',
                                        'data-method' => 'POST',
                                        'data-params' => [
                                            'id' => $gateway['id'],
                                            'status' => Sites::STATUS_ACTIVE,
                                        ],
                                    ])?>
                                <?php elseif (Sites::STATUS_FROZEN == $gateway['status']): ?>
                                <?= Html::a(Yii::t('app/superadmin', 'gateways.list.action.terminate'), Url::toRoute(['/gateways/change-status']), [
                                    'class' => 'dropdown-item',
                                    'data-title' => Yii::t('app/superadmin', 'gateways.list.terminated'),
                                    'data-method' => 'POST',
                                    'data-params' => [
                                        'id' => $gateway['id'],
                                        'status' => Sites::STATUS_TERMINATED,
                                    ],
                                ])?>
                                <?php endif; ?>

                                <?= Html::a(Yii::t('app/superadmin', 'gateways.list.action.sign_in_as_admin'),
                                    Url::toRoute(['/gateways/sign-in-as-admin', 'id' => $gateway['id']]), [
                                    'class' => 'dropdown-item',
                                    'target' => '_blank',
                                ])?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <div class="row">
        <div class="col-md-6">
            <?= LinkPager::widget([
                'pagination' => $gateways['pages'],
            ]); ?>
        </div>
        <div class="col-md-6 text-md-right">
            <?= CountPagination::widget([
                'pages' => $gateways['pages'],
                'params' => array_merge($filters, ['page_size' => $pageSize])
            ]) ?>
        </div>
    </div>
</div>

