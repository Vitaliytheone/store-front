<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \control_panel\models\forms\OrderPanelForm */
    /* @var $note string */
    /* @var $subdomainNote string */
    /* @var $user \common\models\panels\Customers */

    use control_panel\components\ActiveForm;
    use control_panel\models\forms\OrderPanelForm;
    use yii\bootstrap\Html;

    $this->context->addModule('orderDomainController');
    $this->context->addModule('orderController');
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
                'id' => 'order-form',
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
                                    <?= Html::radio(Html::getInputName($model, 'has_domain'), $id == $model->has_domain, [
                                        'value' => $id,
                                        'class' => 'has_domain'
                                    ])?>
                                    <?= $label ?>
                                </label>
                            </div>
                        <?php endforeach; ?>

                    <?= $form->errorSummary($model, [
                        'id' => 'orderDomainError'
                    ]); ?>

                <div class="<?= (OrderPanelForm::HAS_DOMAIN == $model->has_domain || $model->hasErrors() || OrderPanelForm::HAS_SUBDOMAIN == $model->has_domain ? '' : 'hidden') ?>" id="orderBlock">
                    <?= $this->render('layouts/_order_panel_block', [
                        'form' => $form,
                        'model' => $model,
                        'note' => $note,
                        'subdomainNote' => $subdomainNote,
                    ])?>
                </div>

                <div class="<?= (OrderPanelForm::HAS_NOT_DOMAIN == $model->has_domain && !$model->hasErrors() ? '' : 'hidden') ?>" id="orderDomainBlock">
                    <?= $this->render('layouts/_order_domain_block', [
                        'form' => $form,
                        'model' => $model
                    ])?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

