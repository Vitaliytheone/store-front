<?php
    /* @var $this yii\web\View */
    /* @var $panels \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $navs \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $status */
    /* @var $plans */
    /* @var $filters */

    use my\helpers\Url;

    $this->context->addModule('superadminPanelsController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/child-panels', 'status' => $code]) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <form class="form-inline" method="GET" id="panelsSearch" action="<?=Url::toRoute(array_merge(['/child-panels'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="Search child panels" value="<?=$filters['query']?>">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>
    <?= $this->render('layouts/_panels_list', [
        'panels' => $panels,
        'plans' => $plans,
        'filters' => $filters
    ])?>
</div>

<?= $this->render('layouts/_upgrade_modal') ?>
<?= $this->render('layouts/_change_domain_modal') ?>
<?= $this->render('layouts/_edit_expiry_modal') ?>
<?= $this->render('layouts/_edit_providers_modal') ?>
