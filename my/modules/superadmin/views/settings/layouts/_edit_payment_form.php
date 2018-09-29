<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\EditPaymentForm */
    /* @var $payment \common\models\panels\Params */
    /* @var $form my\components\ActiveForm */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use common\models\panels\Params;
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

<?php foreach ($model->details as $name => $value) : ?>
    <div class="form-group">
        <label for=""><?= $model->getAttributeLabel($name) ?></label>
        <?php if ($name == 'visibility') : ?>
            <?= Html::dropDownList('EditPaymentForm[details][' . $name . ']', $value, Params::getVisibilityList(), [
                'class' => 'form-control'
            ]) ?>
        <?php else : ?>
        <?= Html::textInput('EditPaymentForm[details][' . $name . ']', $value, [
            'class' => 'form-control'
        ])?>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php ActiveForm::end(); ?>