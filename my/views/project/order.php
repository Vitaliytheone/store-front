<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \my\models\forms\CreateOrderForm */
    /* @var $note string */

    use my\components\ActiveForm;
    use my\models\forms\CreateOrderForm;
    use yii\bootstrap\Html;

    $this->context->addModule('orderDomainController');
    $this->context->addModule('orderPanelController');
?>
<div class="row">
  <div class="col-lg-12">
    <h2 class="page-header"><?= Yii::t('app', 'panels.order.order_panel')?></small></h2>
  </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">

            <?php $form = ActiveForm::begin([
                'id' => 'order-panel-form',
                'fieldConfig' => [
                    'template' => "{label}{input}",
                    'options' => [
                        'tag' => false,
                    ],
                ]
            ]);?>

                <div class="panel-body">
                    <?php foreach ($model->getHasDomainsLabels() as $id => $label) : ?>
                        <div class="radio">
                            <label>
                                <?= Html::radio('CreateOrderForm[has_domain]', $id == $model->has_domain, [
                                    'value' => $id,
                                    'class' => 'has_domain'
                                ])?>
                                <?= $label ?>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <?= $form->errorSummary($model); ?>
                </div>



                <div class="<?= (CreateOrderForm::HAS_DOMAIN == $model->has_domain || $model->hasErrors() ? '' : 'hidden') ?>" id="orderPanelBlock">
                    <?= $this->render('layouts/_order_panel_block', [
                        'form' => $form,
                        'model' => $model,
                        'note' => $note
                    ])?>
                </div>

                <div class="<?= (CreateOrderForm::HAS_NOT_DOMAIN == $model->has_domain && !$model->hasErrors() ? '' : 'hidden') ?>" id="orderDomainBlock">
                    <?= $this->render('layouts/_order_domain_block', [
                        'form' => $form,
                        'model' => $model
                    ])?>
                </div>

                <?= $this->render('layouts/_order_domain_modal', [
                    'form' => $form,
                    'model' => $model
                ])?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

