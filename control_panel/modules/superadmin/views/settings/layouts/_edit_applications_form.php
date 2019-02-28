<?php
/* @var $this yii\web\View */
/* @var $model superadmin\models\forms\EditApplicationsForm */
/* @var $params \common\models\panels\Params */

/* @var $form control_panel\components\ActiveForm */

use control_panel\components\ActiveForm;
use control_panel\helpers\Url;
use yii\bootstrap\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'editApplicationsForm',
    'action' => Url::toRoute(['/settings/edit-application', 'code' => $params->code]),
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


<?php foreach ((array)$model->options as $name => $value) : ?>
    <div class="form-group">
        <?= Html::label($model->getAttributeLabel($name), $name) ?>
        <?= Html::textInput($model->formName() . "[options][{$name}]", $value, ['id' => $name, 'class' => 'form-control']) ?>
    </div>
<?php endforeach; ?>

<?php ActiveForm::end(); ?>