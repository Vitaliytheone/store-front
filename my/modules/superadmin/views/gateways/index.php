<?php
/* @var $this yii\web\View */
/* @var $gateways array */
/* @var $navs array */
/* @var $status string */
/* @var $filters array */
/* @var $pageSize  */


use my\helpers\Url;
use my\helpers\SpecialCharsHelper;

$this->context->addModule('superadminGatewaysController');
$action = $this->context->activeTab;
?>
<ul class="nav mb-3 nav-pills" role="tablist">
    <li class="mr-auto">
        <ul class="nav nav-pills">
            <?php foreach ($navs as $code => $label) : ?>
                <li class="nav-item"><a class="nav-link <?= ($code === $status ? 'active' : '') ?>" role="tab" href="<?= Url::toRoute($code === 'all' ? ["/$action", 'page_size' => $pageSize] : ["/$action", 'status' => $code, 'page_size' => $pageSize]) ?>"><?= $label ?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <li class="ml-auto">
        <form class="form-inline" method="GET" id="gatewaySearch" action="<?=Url::toRoute(array_merge(["/$action"], $filters, ['query' => null, 'page_size' => $pageSize]))?>">
            <div class="input-group">
                <input type="text" class="form-control" name="query"
                       placeholder="<?= Yii::t('app/superadmin', 'gateways.search')?>"
                       value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                <div class="input-group-append">
                    <button class="btn btn-light" id="submitSearch" type="button"><span class="fa fa-search" ></span></button>
                </div>
            </div>
        </form>
    </li>
</ul>
<div class="tab-content">
    <?= $this->render('layouts/_gateways_list', [
        'gateways' => $gateways,
        'filters' => $filters,
        'pageSize' => $pageSize,
        'action' => $action
    ])?>
</div>
