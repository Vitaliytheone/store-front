<?php
/* @var $this yii\web\View */
/* @var $domains \my\modules\superadmin\models\search\DomainsSearch */
/* @var $navs \my\modules\superadmin\models\search\DomainsSearch */
/* @var $status */

use my\helpers\Url;

$this->context->addModule('superadminDomainsController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <?php $code = is_numeric($code) ? $code : null;?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/domains', 'status' => $code]) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <form class="form-inline" method="GET" id="domainsSearch" action="<?=Url::toRoute(array_merge(['/domains'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'domains.list.search')?>" value="<?=$filters['query']?>">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>

    <?= $this->render('layouts/_domains_list', [
        'domains' => $domains
    ])?>
</div>
<?= $this->render('layouts/_domain_details_modal')?>