<?php
/* @var $this yii\web\View */
/* @var $stores \my\modules\superadmin\models\search\StoresSearch */
/* @var $navs \my\modules\superadmin\models\search\StoresSearch */
/* @var $status array */
/* @var $filters array */

use my\helpers\Url;

$this->context->addModule('superadminStoresController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/stores', 'status' => $code]) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <form class="form-inline" method="GET" id="storesSearch" action="<?=Url::toRoute(array_merge(['/stores'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'stores.list.search')?>" value="<?=$filters['query']?>">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>
    <?= $this->render('layouts/_stores_list', [
        'stores' => $stores,
        'filters' => $filters
    ])?>
</div>
