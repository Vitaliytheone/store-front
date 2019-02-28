<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \control_panel\models\forms\OrderDomainForm */

    use control_panel\helpers\Url;
    use control_panel\components\ActiveForm;

    $this->context->addModule('orderDomainController', [
        'orderDomainForm' => '#order-domain-form'
    ]);
?>
<div class="row">
  <div class="col-lg-12">
    <h2 class="page-header"><?= Yii::t('app', 'panels.order_domain.header')?></small></h2>
  </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">

            <?php $form = ActiveForm::begin([
                'id' => 'order-domain-form',
                'action' => Url::toRoute('/domains/order'),
                'fieldConfig' => [
                    'template' => "{label}{input}",
                    'options' => [
                        'tag' => false,
                    ],
                ]
            ]);?>

                <div class="panel-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'orderDomainError'
                    ]); ?>
                </div>

                <div id="orderDomainBlock">
                    <?= $this->render('/project/layouts/_order_domain_block', [
                        'form' => $form,
                        'model' => $model
                    ])?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

