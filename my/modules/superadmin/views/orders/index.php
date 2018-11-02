<?php
    /* @var $this yii\web\View */
    /* @var $orders \superadmin\models\search\OrdersSearch */
    /* @var $navs \superadmin\models\search\OrdersSearch */
    /* @var $filters */
    /* @var $items */
    /* @var $status */

    use my\helpers\Url;
    use my\helpers\SpecialCharsHelper;
    use my\components\ActiveForm;

    $this->context->addModule('superadminOrdersController');
?>

<ul class="nav mb-3">
    <li class="mr-auto">
        <ul class="nav nav-pills">
            <?php foreach ($navs as $code => $label) : ?>
                <?php $code = is_numeric($code) ? $code : null;?>
                <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/orders', 'status' => $code]) ?>"><?= $label ?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <li>
        <?php $form = ActiveForm::begin([
                'id' => 'ordersSearch',
                'method' => 'get',
                'action' => Url::toRoute(array_merge(['/orders'], $filters, ['query' => null])),
                'options' => [
                    'class' => "form",
                ],
        ]); ?>
            <div class="input-group">
                <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'orders.search.placeholder') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                <div class="input-group-append">
                    <button class="btn btn-light" type="submit"><span class="fa fa-search" id="submitSearch"></span></button>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </li>
</ul>

<?= $this->render('layouts/_orders_list', [
        'orders' => $orders,
        'filters' => $filters,
        'items' => $items
])?>


<?= $this->render('layouts/_order_details_modal') ?>
