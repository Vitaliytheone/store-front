<?php
/* @var $this yii\web\View */
/* @var $model superadmin\models\forms\EditApplicationsForm */
/* @var $payment \common\models\panels\Params */

/* @var $form my\components\ActiveForm */

use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\Params;
use yii\bootstrap\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'editApplicationsForm',
    'action' => Url::toRoute(['/settings/edit-applications', 'code' => $params->code]),
    'options' => [
        'class' => 'form',
    ],
    'fieldClass' => 'yii\bootstrap\ActiveField',
    'fieldConfig' => [
        'template' => "{label}\n{input}",
    ],
]); ?>

<?= $form->errorSummary($model, [
    'id' => 'editApplicationsError'
]); ?>


<?= $form->field($model, 'code') ?>
<?php foreach ((array)$model as $name => $value) : ?>
    <div class="form-group">
        <label for=""><?= $model->getAttributeLabel($name) ?></label>
        <?= Html::textInput($model->formName() . "[{$name}]", $value, ['class' => 'form-control']) ?>
    </div>
<?php endforeach; ?>

<?php ActiveForm::end(); ?>