<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \my\models\forms\OrderDomainForm */

    use my\helpers\Url;
    use my\components\ActiveForm;

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
                    <?= $form->errorSummary($model); ?>
                </div>

                <div id="orderDomainBlock">
                    <?= $this->render('/project/layouts/_order_domain_block', [
                        'form' => $form,
                        'model' => $model
                    ])?>
                </div>

                <?= $this->render('/project/layouts/_order_domain_modal', [
                    'form' => $form,
                    'model' => $model
                ])?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
