<?php
    /* @var $this yii\web\View */
    /* @var $payments \superadmin\models\search\PaymentMethodsSearch */

    use my\helpers\Url;

    $this->context->addModule('superadminPaymentGatewayController');
?>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group list-group__custom">
                <?= $this->render('layouts/_menu'); ?>
            </div>
        </div>
        <div class="col-md-9">
            <?= $this->render('layouts/_payment_gateway_list', [
                    'payments' => $payments
            ]); ?>
        </div>
    </div>
</div>
<?= $this->render('layouts/_edit_payment_modal'); ?>
