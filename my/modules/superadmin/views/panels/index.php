<?php
    /* @var $this yii\web\View */
    /* @var $panels \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $navs \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $status string*/
    /* @var $plans array*/
    /* @var $filters array */
    /* @var $pageSizes array */

    use my\helpers\Url;

    $this->context->addModule('superadminPanelsController');
    $action = $this->context->activeTab;
    $pageSize = $panels['pages']->pageSize;
?>
    <ul class="nav mb-3 nav-pills" role="tablist">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <li class="nav-item"><a class="nav-link <?= ($code === $status ? 'active' : '') ?>" role="tab" href="<?= Url::toRoute($code === 'all' ? ["/$action", 'pageSize' => $pageSize] : ["/$action", 'status' => $code, 'pageSize' => $pageSize]) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li class="ml-auto">
            <form class="form-inline" method="GET" id="panelsSearch" action="<?=Url::toRoute(array_merge(["/$action"], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'panels.search')?>" value="<?=$filters['query']?>">
                    <div class="input-group-append">
                        <button class="btn btn-light" id="submitSearch" type="button"><span class="fa fa-search" ></span></button>
                    </div>
                </div>
            </form>
        </li>
    </ul>
    <div class="tab-content">
        <?= $this->render('layouts/_panels_list', [
            'panels' => $panels,
            'plans' => $plans,
            'filters' => $filters,
            'pageSizes' =>  $pageSizes,
            'action' => $action
        ])?>
    </div>

<?php $this->beginBlock('modals'); ?>
<?php if ($action == 'panels') : ?>
    <?= $this->render('layouts/_downgrade_modal') ?>
<?php else : ?>
    <?= $this->render('layouts/_upgrade_modal') ?>
<?php endif; ?>
<?= $this->render('layouts/_change_domain_modal', ['action' => $action]) ?>
<?= $this->render('layouts/_edit_expiry_modal', ['action' => $action]) ?>
<?= $this->render('layouts/_edit_providers_modal', ['action' => $action]) ?>
<?= $this->render('layouts/_edit_panels_modal', ['action' => $action]) ?>
<?= $this->endBlock();?>
