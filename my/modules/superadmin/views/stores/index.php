<?php
/* @var $this yii\web\View */
/* @var $stores \superadmin\models\search\StoresSearch */
/* @var $navs \superadmin\models\search\StoresSearch */
/* @var $status array */
/* @var $filters array */

use my\helpers\Url;
use my\components\ActiveForm;
use my\helpers\SpecialCharsHelper;

$this->context->addModule('superadminStoresController');
?>
<ul class="nav nav-pills mb-3" role="tablist">
    <?php foreach ($navs as $code => $label) : ?>
    <li class="nav-item">
        <a class="nav-link <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute($code === 'all' ? '/stores' : ['/stores', 'status' => $code, 'page_size' => $stores['pages']->pageSize]) ?>"><?= $label ?></a>
    </li>
    <?php endforeach; ?>
    <li class="ml-auto">
        <?php $form = ActiveForm::begin([
            'id' => 'storesSearch',
            'method' => 'get',
            'action' => Url::toRoute(array_merge(['/stores'], $filters, ['query' => null])),
            'options' => [
                'class' => "form-inline",
            ],
        ]); ?>
        <div class="input-group">
            <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'stores.list.search')?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
            <div class="input-group-append">
                <button class="btn btn-light" type="submit"><span class="fa fa-search"></span></button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </li>
</ul>
<?= $this->render('layouts/_stores_list', [
    'stores' => $stores,
    'filters' => $filters
])?>

<?= $this->render('layouts/_edit_store_modal')?>
<?= $this->render('layouts/_edit_expiry_modal')?>
<?= $this->render('layouts/_change_domain_modal')?>

