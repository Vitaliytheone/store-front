<?php
/* @var $this yii\web\View */
/* @var $tickets \common\models\panels\Tickets */
/* @var $ticket \common\models\panels\Tickets */
/* @var $model \my\models\forms\CreateTicketForm */
/* @var $accesses */

use yii\bootstrap\Html;
use yii\widgets\LinkPager;

?>
<div class="row">
  <div class="col-lg-12">
    <h2 class="page-header">
        <?= Yii::t('app', 'support.list.header'); ?> <button class="btn btn-outline btn-success" data-toggle="modal" data-target="#submitTicket"><?= Yii::t('app', 'support.list.create_ticket')?></button>
        <div class="alert alert-danger error-hint hidden" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="content"></span>
        </div>
    </h2>
  </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <?php if (!empty($note)) : ?>
            <div class="alert alert-info">
                <?= $note ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($tickets['models'])): ?>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'support.list.ticket_column')?></th>
                        <th><?= Yii::t('app', 'support.list.subject_column')?></th>
                        <th><?= Yii::t('app', 'support.list.status_column')?></th>
                        <th><?= Yii::t('app', 'support.list.created_column')?></th>
                        <th nowrap><?= Yii::t('app', 'support.list.last_update_column')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets['models'] as $ticket): ?>
                        <?php
                            if ($ticket->updated_at == 0) {
                                $ticket->updated_at = $ticket->created_at;
                            }
                        ?>
                        <tr>
                            <td><?= $ticket->id; ?></td>
                            <td>
                                <?php if ($ticket->is_admin == 1) : ?>
                                    <?= Html::a('<b>'.htmlspecialchars($ticket->subject).'</b>', '/ticket/' . $ticket->id, [
                                        'data-subject' => htmlspecialchars($ticket->subject),
                                        'style' => 'cursor:pointer;',
                                        'class' => 'show-ticket',
                                        'onclick' => 'return false',
                                    ])?>
                                <?php else : ?>
                                    <?= Html::a(htmlspecialchars($ticket->subject), '/ticket/' . $ticket->id, [
                                        'data-subject' => htmlspecialchars($ticket->subject),
                                        'style' => 'cursor:pointer;',
                                        'class' => 'show-ticket',
                                        'onclick' => 'return false',
                                    ])?>
                                <?php endif; ?>
                            </td>
                            <td><?= $ticket->getStatusName(); ?></td>
                            <td><?= $ticket->getFormattedDate('created_at'); ?></td>
                            <td><?= $ticket->getFormattedDate('updated_at'); ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>

        <div class="text-align-center">
            <?= LinkPager::widget([
                'pagination' => $tickets['pages'],
            ]); ?>
        </div>

    </div>
</div>

<?= $this->render('layouts/_view_ticket_modal') ?>

<?= $this->render('layouts/_create_ticket_modal', ['model' => $model]) ?>