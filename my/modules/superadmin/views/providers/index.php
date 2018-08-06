<?php
    /* @var $this yii\web\View */
    /* @var $providers \my\modules\superadmin\models\search\ProvidersSearch */
    /* @var $navs \my\modules\superadmin\models\search\ProvidersSearch */
    /* @var $filters */
    /* @var $type */

    use my\helpers\Url;
    use my\helpers\SpecialCharsHelper;

    $this->context->addModule('superadminProvidersController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <?php $code = is_numeric($code) ? $code : null;?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $type ? 'active' : '') ?>" href="<?= Url::toRoute($code === null ? '/providers' : array_merge(['/providers'], $filters, ['type' => $code])) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <form class="form-inline" method="GET" id="providersSearch" action="<?=Url::toRoute(array_merge(['/providers'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'providers.list.search') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                    <span class="input-group-btn">
                    <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                </span>
                </div>
            </form>
        </li>
    </ul>

    <?= $this->render('layouts/_providers_list', [
        'providers' => $providers,
        'filters' => $filters
    ])?>
</div>

<?= $this->render('layouts/_projects_modal')?>
<?= $this->render('layouts/_edit_provider_modal')?>
