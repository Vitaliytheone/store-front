<?php
/* @var $models array */
/* @var $query string */
/* @var $selectList array */
/* @var $selectedOption string */

use my\components\ActiveForm;
use my\helpers\Url;
use yii\helpers\Html;

$query = isset($query) ? $query : 'UPDATE `db_name`.`services` SET `provider_id` = `res`, `provider_service_id` = `reid`, `provider_service_params` = `params`;';
$this->context->addModule('superadminDbHelperController');
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-block">
                    <div class="row">
                        <div class="col-lg-6">
                            <?php $form = ActiveForm::begin([
                                'action' => Url::toRoute(['tools/db-helper']),
                                'id' => 'dbHelperForm',
                                'fieldClass' => 'yii\bootstrap\ActiveField',
                                'fieldConfig' => [
                                    'labelOptions' => ['class' => 'form'],
                                ]]); ?>
                            <select class="form-control db_name" name="db_name">
                                <?php foreach ($selectList as $key => $option) : ?>
                                    <option <?= $selectedOption == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $option ?></option>
                                <?php endforeach; ?>
                            </select><br>
                            <?= Html::textarea('query', $query, ['class' => 'query_input form-control', 'rows' => '7']); ?>
                            <br>
                            <?= Html::submitButton(Yii::t('app/superadmin', Yii::t('app/superadmin', 'db_helper.apply_btn')), [
                                'class' => 'btn btn-outline btn-primary',
                                'name' => 'db-helper-button',
                                'id' => 'dbHelperButton'
                            ]) ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                        <div class="col-lg-6">
                            <pre class="query_content">
                                <?php foreach ($models as $model) : ?>
                                    <?= isset($model) ? $model : $query ?>
                                    <br>
                                <?php endforeach; ?>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
