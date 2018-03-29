<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \my\models\forms\OrderStoreForm */
    /* @var $hasStores bool */

    use my\components\ActiveForm;
    use my\models\forms\OrderStoreForm;
    use yii\bootstrap\Html;
    use yii\helpers\Url;

    $hasStores = $model->getUser()->hasStores();

?>
<div class="row">
  <div class="col-lg-12">
      <?php if($hasStores): ?>
            <h2 class="page-header"><?= Yii::t('app', 'stores.order.form.title') ?></small></h2>
      <?php else: ?>
            <h2 class="page-header"><?= Yii::t('app', 'stores.order.form.title_trial') ?></small></h2>
      <?php endif; ?>
  </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">

            <?php $form = ActiveForm::begin([
                'id' => 'order-store-form',
                'action' => Url::toRoute('stores/order'),
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
                    <?= $form->field($model, 'store_name')->textInput(['class' => 'form-control']) ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'store_currency')->dropDownList($model->getCurrencies(), ['class' => 'form-control']) ?>
                </div>

                <hr>
                <div class="form-group">
                    <?= $form->field($model, 'admin_username')->textInput(['class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'admin_password')->passwordInput(['class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'confirm_password')->passwordInput(['class' => 'form-control']) ?>
                </div>
            </div>

            <div class="panel-footer" style="background-color: #fff">
                <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app', 'stores.order.form.submit') ?></button>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

