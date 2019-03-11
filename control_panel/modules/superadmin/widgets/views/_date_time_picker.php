<?php

use yii\helpers\Html;

/* @var $form \control_panel\components\ActiveForm */
/* @var $model yii\base\Model */
/* @var $attribute string */
/* @var $format string */

?>

<div class="input-group date" id="datetimepicker"  data-target-input="nearest" data-format="<?= $format ?>">
    <?= Html::activeInput('text', $model, $attribute, [
        'class' => 'form-control datetimepicker-input',
        'data-target' => '#datetimepicker',
        'id' => 'editexpiryform-expired',
    ]) ?>
    <div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
    </div>
</div>