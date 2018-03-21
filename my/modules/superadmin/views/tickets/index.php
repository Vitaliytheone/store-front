<?php
/* @var $this yii\web\View */
/* @var $tickets \my\modules\superadmin\models\search\TicketsSearch */
/* @var $navs \my\modules\superadmin\models\search\TicketsSearch */
/* @var $status */
/* @var $filters */

use my\helpers\Url;

$this->context->addModule('superadminTicketsController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <?php $code = is_numeric($code) ? (int)$code : $code;?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(array_merge($filters, ['/tickets', 'status' => $code])) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <a id="createTicket" class="btn btn-secondary" href="<?= Url::toRoute('/tickets/create')?>"><?= Yii::t('app/superadmin', 'tickets.btn.create_new')?></a>
            &nbsp;
        </li>
        <li>
            <form class="form-inline" method="GET" id="ticketsSearch" action="<?=Url::toRoute(array_merge(['/tickets'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'tickets.list.search')?>" value="<?=$filters['query']?>">
                    <span class="input-group-btn">
                    <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                </span>
                </div>
            </form>
        </li>
    </ul>

    <?= $this->render('layouts/_tickets_list', [
        'tickets' => $tickets
    ])?>
</div>

<?= $this->render('layouts/_create_ticket_modal')?>