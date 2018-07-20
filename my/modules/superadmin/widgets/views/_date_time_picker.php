<?php

use yii\helpers\Html;

/* @var $form \my\components\ActiveForm */
/* @var $model yii\base\Model */
/* @var $attribute string */
?>

<div class="input-group date" id="datetimepicker" data-target-input="nearest">
    <?= Html::activeInput('text', $model, $attribute, [
        'class' => 'form-control datetimepicker-input',
        'data-target' => '#datetimepicker',
        'id' => 'editexpiryform-expired',
    ]) ?>
    <div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
    </div>
</div>
