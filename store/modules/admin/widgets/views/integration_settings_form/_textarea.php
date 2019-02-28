<?php

/** @var $value array */
/** @var $options string */

use yii\helpers\Html;

?>

<div class="form-group m-form__group">
    <?= Html::label(Yii::t('admin', $value['label']), 'settings-custom__header'); ?>

    <?= Html::textarea('options[' . $value['name'] . ']', $options, [
    'class' => 'form-control m-input',
    'id' => 'settings-custom__header',
    'rows' => '14',
    ]); ?>
</div>

