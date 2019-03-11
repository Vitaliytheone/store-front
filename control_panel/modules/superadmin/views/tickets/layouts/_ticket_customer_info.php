<?php
/* @var $ticket \common\models\sommerces\Tickets */
/* @var $admins array */
/* @var $statuses array */
/* @var $stores array */
/* @var $ssl array */
/* @var $domains array */

use control_panel\components\ActiveForm;
use control_panel\helpers\Url;
use yii\helpers\Html;
use common\models\sommerces\Stores;

?>

<div class="bg-light rounded ticket-block__right">

    <div class="ticket-info__block">

        <div class="ticket-info__header">
            <div class="ticket-info__block-username">
                <a href="<?= Url::toRoute(['/customers', 'query' => $ticket->customer->email]); ?>" target="_blank">
                    <?= $ticket->customer->email ?>
                </a>
            </div>
            <div class="ticket-info__block-autorization">
                <a href="<?= Url::toRoute(['/customers/auth', 'id' => $ticket->customer->id])?>" target="_blank" data-placement="top" title="" data-original-title="<?=Yii::t('app/superadmin', 'tickets.sign_in_as_customer')?>">
                    <span class="my-icons my-icons-autorization"></span>
                </a>
            </div>
        </div>

        <table class="table table-sm table-ticket table-middle">
            <tbody>
            <tr>
                <td><?= Yii::t('app/superadmin', 'tickets.status') ?></td>
                <td>

                    <div class="dropdown">
                        <button class="btn ticket-info__btn dropdown-toggle" type="button" data-toggle="dropdown">
                            <?= $ticket->getStatusName() ?>
                        </button>
                        <?php $form = ActiveForm::begin([
                            'id' => 'changed-status-form',
                            'action' => Url::toRoute(['/tickets/change-status']),
                            'fieldConfig' => [
                                'template' => "{label}\n{input}",
                                'labelOptions' => ['class' => 'control-label'],
                            ],
                        ]); ?>
                        <div class="dropdown-menu dropdown-min-width-240">

                            <div class="ticket-info__actions-dropdown">
                                <div class="form-group">
                                    <select class="form-control" name="status">
                                        <?php $selected = 'selected' ?>
                                        <?php foreach ($statuses as $key => $value) : ?>
                                            <?php if ($ticket->status != $key) : ?>
                                                <option <?= $selected ?> value ="<?= $key ?>"><?= $value ?></option>
                                                <?php $selected = '' ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button id="send-status" class="btn  btn-block btn-primary"><?= Yii::t('app/superadmin', 'tickets.submit') ?></button>
                                </div>
                            </div>
                        </div>
                        <input hidden type="text" name="id" value="<?= $ticket->id ?>"/>
                        <?php ActiveForm::end(); ?>
                    </div>

                </td>
            </tr>
            <tr>
                <td><?= Yii::t('app/superadmin', 'tickets.assignee') ?></td>
                <td>

                    <div class="dropdown">
                        <button class="btn ticket-info__btn dropdown-toggle" id="assigned-toggle" type="button" data-toggle="dropdown">
                            <?= $ticket->assigned ?  $ticket->assigned->getFullName() :  Yii::t('app/superadmin', 'tickets.unassigned') ?>
                        </button>
                        <?php $form = ActiveForm::begin([
                            'id' => 'changed-status-form',
                            'action' => Url::toRoute(['/tickets/change-assigned']),
                            'fieldConfig' => [
                                'template' => "{label}\n{input}",
                                'labelOptions' => ['class' => 'control-label'],
                            ],
                        ]); ?>
                        <div class="dropdown-menu dropdown-min-width-240">
                            <div class="ticket-info__actions-dropdown">

                                <div class="form-group">
                                    <?php $selected = 'selected' ?>
                                    <select class="form-control" name="assigned-select" id="assigned-select">
                                        <?php foreach ($admins as $key => $admin) : ?>
                                            <?php if ($ticket->assigned_admin_id != $key) : ?>
                                                <option <?= $selected ?> value ="<?= $key ?>"><?= $admin['first_name']. ' ' . $admin['last_name'] ?></option>
                                                <?php $selected = '' ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea name="comment" class="form-control" placeholder="<?= Yii::t('app/superadmin', 'tickets.customer_info.textarea_placeholder') ?>" rows="7"></textarea>
                                </div>
                                <div class="form-group">
                                    <button id="assigned" class="btn btn-block btn-primary"><?= Yii::t('app/superadmin', 'tickets.submit') ?></button>
                                </div>
                            </div>
                        </div>
                        <input hidden type="text" name="ticketId" value="<?= $ticket->id ?>"/>
                        <?php ActiveForm::end(); ?>
                    </div>

                </td>
            </tr>
            </tbody>
        </table>

    </div>

    <?php if ($stores) : ?>
        <div class="ticket-info__block-header" data-toggle="collapse" href="#ticket-block-id-6">
            <div class="ticket-info__block-header-title"><?= count($stores) ?> <?= Yii::t('app/superadmin', 'pages.title.stores') ?></div>
        </div>

        <div class="ticket-info__block in collapse show" id="ticket-block-id-6">
            <table class="table table-sm table-ticket">
                <tbody>
                <?php foreach ($stores as $item) :
                    $loginUrl = Url::toRoute(['/stores/sign-in-as-admin', 'id' => $item->id]);
                    ?>
                    <tr>
                        <td>
                            <?= Html::a($item->getBaseDomain(), Url::toRoute(['/stores', 'id' => $item->id]), ['target' => '_blank'])?>
                            <?php if (Stores::STATUS_ACTIVE != $item->status) : ?>
                                <span class="badge badge-primary"><?= Stores::getStatuses()[$item->status] ?></span>
                            <?php endif; ?>
                            <a href="//<?= Yii::$app->params['my_domain'] . '/redirect?url=' . $item->getBaseDomain() ?>" target="_blank">
                                <span class="fa fa-external-link"></span>
                            </a>
                        </td>
                        <td class="text-right">
                            <a href="<?=$loginUrl?>" target="_blank" data-placement="top" title="" data-original-title="<?=Yii::t('app/superadmin', 'tickets.sign_in_as_admin')?>">
                                <span class="my-icons my-icons-autorization"></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>


    <?php if ($domains) : ?>
        <div class="ticket-info__block-header" data-toggle="collapse" href="#ticket-block-id-3">
            <div class="ticket-info__block-header-title"><?= count($domains) ?> <?= Yii::t('app/superadmin', 'pages.title.domains') ?></div>
        </div>

        <div class="ticket-info__block in collapse show" id="ticket-block-id-3">
            <table class="table table-sm table-ticket">
                <tbody>
                <?php foreach ($domains as $domain) : ?>
                    <tr>
                        <td>
                            <?= Html::a($domain->getDomain(), Url::toRoute(['/domains', 'id' => $domain->id]),['target' => '_blank'])?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($ssl) : ?>
        <div class="ticket-info__block-header" data-toggle="collapse" href="#ticket-block-id-2">
            <div class="ticket-info__block-header-title"><?= count($ssl) ?> <?= Yii::t('app/superadmin', 'tickets.ssl') ?></div>
        </div>

        <div class="ticket-info__block in collapse show" id="ticket-block-id-2">
            <table class="table table-sm table-ticket">
                <tbody>
                <?php foreach ($ssl as $item) : ?>
                    <tr>
                        <td>
                            <?= Html::a($item->getDomain(), Url::toRoute(['/ssl', 'id' => $item->id]),['target' => '_blank'])?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
