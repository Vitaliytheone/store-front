<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \control_panel\models\forms\OrderSslPaidForm */

use yii\helpers\Html;
use control_panel\components\ActiveForm;
use yii\helpers\ArrayHelper;

?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"><?= Yii::t('app', 'ssl.order.header')?></small></h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <?php $form = ActiveForm::begin([
                'id' => 'order-ssl-form',
                'fieldConfig' => [
                    'template' => "{label}{input}",
                ]
            ]);?>
                <div class="panel-body">

                    <?= $form->errorSummary($model); ?>

                    <?= $form->field($model, 'pid')->dropDownList($model->getAllProjectsDomains(true)) ?>

                    <?= $form->field($model, 'item_id')->dropDownList($model->getSslItems()) ?>

                    <hr />

                    <?php foreach ($model->getDetails() as $name) : ?>
                        <?php if (in_array($name, [
                            'admin_country',
                            'tech_country'
                        ])) : ?>
                            <?= $form->field($model, $name)->dropDownList($model->getCountries(), ['prompt' => '']) ?>
                        <?php else : ?>
                            <?= $form->field($model, $name) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="panel-footer" style="background-color: #fff">
                    <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app', 'ssl.order.btn_submit')?></button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
