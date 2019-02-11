<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \my\models\forms\CreateChildForm */
    /* @var $note string */
    /* @var $user \common\models\panels\Customers */

    use my\components\ActiveForm;
    use my\models\forms\CreateChildForm;
    use yii\bootstrap\Html;

    $this->context->addModule('orderDomainController');
    $this->context->addModule('orderController');

?>
<div class="row">
  <div class="col-lg-12">
    <h2 class="page-header"><?= Yii::t('app', 'child_panels.order.order_panel')?></small></h2>
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
                    <?php if ($user->can('domains')): ?>
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
                    <?php endif; ?>

                    <?= $form->errorSummary($model, [
                        'id' => 'orderDomainError'
                    ]); ?>

                    <div class="<?= (CreateChildForm::HAS_DOMAIN == $model->has_domain || $model->hasErrors() ? '' : 'hidden') ?>" id="orderBlock">
                        <?= $this->render('layouts/_order_panel_block', [
                            'form' => $form,
                            'model' => $model,
                            'note' => $note
                        ])?>
                    </div>

                    <div class="<?= (CreateChildForm::HAS_NOT_DOMAIN == $model->has_domain && !$model->hasErrors() ? '' : 'hidden') ?>" id="orderDomainBlock">
                        <?= $this->render('layouts/_order_domain_block', [
                            'form' => $form,
                            'model' => $model
                        ])?>
                    </div>

                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

