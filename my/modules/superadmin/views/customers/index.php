<?php
    /* @var $this yii\web\View */
    /* @var $customers \my\modules\superadmin\models\search\CustomersSearch */
    /* @var $navs \my\modules\superadmin\models\search\CustomersSearch */
    /* @var $status int|string */
    /* @var $filters array */

    use my\helpers\Url;
    use my\components\ActiveForm;
    use my\helpers\SpecialCharsHelper;

    $this->context->addModule('superadminCustomersController');
?>

    <ul class="nav nav-pills mb-3" role="tablist">
                <?php foreach ($navs as $code => $label) : ?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/customers', 'status' => $code, 'page_size' => $customers['pages']->pageSize]) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
        <li class="ml-auto">
            <?php $form = ActiveForm::begin([
                'id' => 'customersSearch',
                'method' => 'get',
                'action' => Url::toRoute(array_merge(['/customers'], $filters, ['query' => null])),
                'options' => [
                    'class' => "form",
                ],
            ]); ?>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="<?= Yii::t('app/superadmin', 'customers.list.search_placeholder')?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query'])?>" name="query">
                <div class="input-group-append">
                    <button class="btn btn-light" type="submit"><span class="fa fa-search"></span></button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </li>
    </ul>

    <?= $this->render('layouts/_customers_list', [
        'customers' => $customers,
        'filters' => $filters,
    ])?>


<?= $this->render('layouts/_edit_customer_modal')?>
<?= $this->render('layouts/_set_password_modal')?>
