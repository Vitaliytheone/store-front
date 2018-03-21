<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\EditPaymentForm */
    /* @var $payment \common\models\panels\PaymentGateway */
    /* @var $form my\components\ActiveForm */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use common\models\panels\PaymentGateway;
    use yii\bootstrap\Html;
?>

<?php $form = ActiveForm::begin([
    'id' => 'editPaymentForm',
    'action' => Url::toRoute(['/settings/edit-payment', 'id' => $payment->id]),
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

<?= $form->field($model, 'visibility')->dropDownList(PaymentGateway::getVisibilityList()) ?>

<?php foreach ($model->details as $name => $value) : ?>
    <div class="form-group">
        <label for=""><?= $model->getAttributeLabel($name) ?></label>
        <?= Html::textInput('EditPaymentForm[details][' . $name . ']', $value, [
            'class' => 'form-control'
        ])?>
    </div>
<?php endforeach; ?>

<?php ActiveForm::end(); ?>