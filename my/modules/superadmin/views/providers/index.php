<?php
    /* @var $this yii\web\View */
    /* @var $providers \superadmin\models\search\ProvidersSearch */
    /* @var $navs \superadmin\models\search\ProvidersSearch */
    /* @var $filters */
    /* @var $type */
    /* @var $plans */

    use my\helpers\Url;
    use my\helpers\SpecialCharsHelper;
    use my\components\ActiveForm;

    $this->context->addModule('superadminProvidersController');
?>

    <ul class="nav nav-pills mb-3" role="tablist">
        <?php foreach ($navs as $code => $label) : ?>
            <?php $code = is_numeric($code) ? $code : null;?>
            <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $type ? 'active' : '') ?>" href="<?= Url::toRoute($code === null ? '/providers' : array_merge(['/providers'], $filters, ['type' => $code, 'page_size' => $providers['pages']->pageSize])) ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
        <li class="ml-auto">
            <?php $form = ActiveForm::begin([
                'id' => 'providersSearch',
                'method' => 'get',
                'action' => Url::toRoute(array_merge(['/providers'], $filters, ['query' => null])),
                'options' => [
                    'class' => "form",
                ],
            ]) ?>
                <div class="input-group input-group__buttons">
                    <a href="<?= Url::toRoute('/providers/create')?>" class="btn btn-link" id="createProvider">
                        <?= Yii::t('app/superadmin', 'providers.modal_create.header')?>
                    </a>
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'providers.list.search') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">

                    <div class="input-group-append">
                        <button class="btn btn-light" type="submit"><span class="fa fa-search" id="submitSearch"></span></button>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </li>
    </ul>

    <?= $this->render('layouts/_providers_list', [
        'providers' => $providers,
        'filters' => $filters,
        'scripts' => $scripts,
    ])?>

<?= $this->render('layouts/_projects_modal')?>
<?= $this->render('layouts/_edit_provider_modal') ?>
<?= $this->render('layouts/_create_provider_modal') ?>
