<?php
    /* @var $this yii\web\View */
    /* @var $customers \my\modules\superadmin\models\search\CustomersSearch */
    /* @var $navs \my\modules\superadmin\models\search\CustomersSearch */
    /* @var $status */

    use my\helpers\Url;

    $this->context->addModule('superadminCustomersController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">
            <ul class="nav nav-pills">
                <?php foreach ($navs as $code => $label) : ?>
                    <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/customers', 'status' => $code]) ?>"><?= $label ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li>
            <form class="form-inline" method="GET" id="customersSearch" action="<?=Url::toRoute(array_merge(['/customers'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="Search customers" value="<?=$filters['query']?>">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>

    <?= $this->render('layouts/_customers_list', [
        'customers' => $customers
    ])?>
</div>

<?= $this->render('layouts/_edit_customer_modal')?>
<?= $this->render('layouts/_set_password_modal')?>
