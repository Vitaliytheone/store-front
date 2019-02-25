<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model OrderGatewayForm */
/* @var $note string */


use control_panel\models\forms\OrderGatewayForm;
use control_panel\components\ActiveForm;

$this->context->addModule('orderDomainController');
$this->context->addModule('orderController');

?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"><?= Yii::t('app', 'gateways.order.gateway_block_header')?></small></h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">

            <?php $form = ActiveForm::begin([
                'id' => 'order-form',
                'fieldConfig' => [
                    'template' => "{label}{input}",
                    'options' => [
                        'tag' => false,
                    ],
                ]
            ]);?>

            <div class="panel-body">

                <?= $form->errorSummary($model); ?>

                <div class="form-group">
                    <label><?= Yii::t('app', 'form.order_gateway.domain')?></label>
                    <?= $form->field($model, 'domain')->label(false)->textInput([
                        'id' => 'domain',
                        'value' => $model->getDomain(),
                        'autofocus' => true,
                        'class' => 'form-control',
                        'readonly' => OrderGatewayForm::HAS_NOT_DOMAIN == $model->has_domain ? 'readonly' : null
                    ]) ?>
                </div>

                <?php if (!empty($note)) : ?>
                    <div class="alert alert-info" id="orderNote">
                        <?= $note ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <?= $form->field($model, 'username')->textInput(['class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'password_confirm')->passwordInput(['class' => 'form-control']) ?>
                </div>
            </div>

            <div class="panel-footer" style="background-color: #fff">
                <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app', 'gateways.order.gateway_block_submit')?></button>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>