<?php
/* @var $this yii\web\View */
/* @var $tickets array */
/* @var $navs array */
/* @var $status int */
/* @var $superAdmins array */
/* @var $superAdminCount array */
/* @var $filters array */
/* @var $assignee int */

use my\helpers\Url;

$this->context->addModule('superadminTicketsController');
?>
<ul class="nav nav-pills mb-3" role="tablist">
    <?php foreach ($navs as $code => $label) : ?>
        <?php $code = is_numeric($code) ? (int)$code : $code;?>
        <li class="nav-item"><a class="nav-link status-tab <?= ($code === $status ? 'active' : '') ?>" data-toggle="pill" href="<?= Url::toRoute($code === 'all' ? '/tickets' : array_merge($filters, ['/tickets', 'assignee' => null, 'status' => $code])) ?>"><?= $label ?></a></li>
    <?php endforeach; ?>
    <li class="ml-auto">
        <form class="form-inline" method="GET" id="ticketsSearch" action="<?=Url::toRoute(array_merge(['/tickets'], $filters, ['query' => null]))?>">
            <div class="input-group input-group__buttons">
                <a href="<?= Url::toRoute('/tickets/create')?>" class="btn btn-link" id="new-ticket" data-toggle="modal" data-target="#create-ticket">
                    <?= Yii::t('app/superadmin', 'tickets.btn.create_new')?>
                </a>
                <input name="query" type="text" class="form-control" placeholder="<?= Yii::t('app/superadmin', 'tickets.list.search')?>" value="<?= htmlspecialchars($filters['query'], ENT_QUOTES)?>">
                <div class="input-group-append">
                    <button id="search" class="btn btn-light" type="button"><span class="fa fa-search"></span></button>
                </div>
            </div>
        </form>
    </li>
</ul>


<?= $this->render('layouts/_tickets_list', [
    'tickets' => $tickets,
    'superAdmins' => $superAdmins,
    'superAdminCount' => $superAdminCount,
    'filters' => $filters,
    'assignee' => $assignee
])?>
<?php $this->beginBlock('modals'); ?>
    <?= $this->render('layouts/_create_ticket_modal.php')?>
<?= $this->endBlock();?>