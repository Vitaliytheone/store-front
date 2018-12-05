<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \my\models\forms\OrderStoreForm */
    /* @var $note string */

    use my\models\forms\OrderStoreForm;
?>

<div class="panel-body">
    <div class="form-group">
        <?= $form->field($model, 'domain')->textInput([
            'id' => 'domain',
            'value' => $model->getDomain(),
            'autofocus' => true,
            'class' => 'form-control',
            'readonly' => OrderStoreForm::HAS_NOT_DOMAIN == $model->has_domain ? 'readonly' : null
        ]) ?>
    </div>

    <?php if (!empty($note)) : ?>
        <div class="alert alert-info" id="orderNote">
            <?= $note ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= $form->field($model, 'store_currency')->dropDownList($model->getCurrencies(), ['class' => 'form-control']) ?>
    </div>

    <hr>

    <div class="form-group">
        <?= $form->field($model, 'admin_email')->textInput(['class' => 'form-control', 'type' => 'email']) ?>
    </div>
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
