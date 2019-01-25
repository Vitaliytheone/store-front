<?php
/* @var $this yii\web\View */
/* @var $domains \superadmin\models\search\DomainsSearch */
/* @var $navs \superadmin\models\search\DomainsSearch */
/* @var $status */

use my\helpers\Url;
use my\helpers\SpecialCharsHelper;

$this->context->addModule('superadminDomainsController');
?>
    <ul class="nav nav-pills mb-3" role="tablist">
        <?php foreach ($navs as $code => $label) : ?>
            <?php $code = is_numeric($code) ? $code : null;?>
            <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/domains', 'status' => $code]) ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
        <li class="ml-auto">
            <form class="form" method="GET" id="domainsSearch" action="<?=Url::toRoute(array_merge(['/domains'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'domains.list.search')?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-light" type="submit"><span class="fa fa-search"></span></button>
                    </div>
                </div>
            </form>
        </li>
    </ul>

    <?= $this->render('layouts/_domains_list', [
        'domains' => $domains,
    ])?>

<?= $this->render('layouts/_domain_details_modal')?>