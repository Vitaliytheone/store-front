<?php
    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model \my\models\forms\OrderPanelForm */
    /* @var $note string */

    use yii\bootstrap\Html;
    use my\models\forms\OrderPanelForm;
?>
<div class="panel-body">

    <div class="form-group">
        <label><?= Yii::t('app', 'panels.order.panel_block_header')?></label>
        <?= $form->field($model, 'domain')->label(false)->textInput([
            'id' => 'domain',
            'value' => $model->getDomain(),
            'autofocus' => true,
            'class' => 'form-control',
            'readonly' => OrderPanelForm::HAS_NOT_DOMAIN == $model->has_domain ? 'readonly' : null
        ]) ?>
    </div>

    <?php if (!empty($note)) : ?>
        <div class="alert alert-info" id="orderNote">
            <?= $note ?>
        </div>
    <?php endif; ?>
    
    <div class="form-group">
        <?= $form->field($model, 'currency')->dropDownList($model->getCurrencies(), ['class' => 'form-control']) ?>
    </div>
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
    <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app', 'panels.order.panel_block_submit')?></button>
</div>