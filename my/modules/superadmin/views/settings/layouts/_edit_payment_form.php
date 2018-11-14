<?php
    /* @var $this yii\web\View */
    /* @var $model superadmin\models\forms\EditPaymentForm */
    /* @var $payment \common\models\panels\Params */
    /* @var $form my\components\ActiveForm */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use common\models\panels\Params;
    use yii\bootstrap\Html;
?>

<?php $form = ActiveForm::begin([
    'id' => 'editPaymentForm',
    'action' => Url::toRoute(['/settings/edit-payment', 'code' => $payment->code]),
    'options' => [
        'class' => "form",
    ],
    'fieldClass' => 'yii\bootstrap\ActiveField',
    'fieldConfig' => [
        'template' => "{label}\n{input}",
    ],
]); ?>

 <?= $form->errorSummary($model, [
    'id' => 'editPaymentError'
]); ?>

<?= $form->field($model, 'name') ?>
<?= $form->field($model, 'visibility')->dropDownList(Params::getVisibilityList()) ?>
<?php foreach ((array)$model->credentials as $name => $value) : ?>
    <div class="form-group">
        <label for=""><?= $model->getAttributeLabel($name) ?></label>
        <?= Html::textInput($model->formName() . "[credentials][$name]", $value, [
            'class' => 'form-control'
        ])?>
    </div>
<?php endforeach; ?>

<?php ActiveForm::end(); ?>