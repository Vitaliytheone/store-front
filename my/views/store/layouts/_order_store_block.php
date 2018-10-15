<?php

?>

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
