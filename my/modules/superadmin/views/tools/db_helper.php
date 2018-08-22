<?php
/* @var $models array */
/* @var $query string */

use my\components\ActiveForm;
use my\helpers\Url;
use yii\helpers\Html;


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
                                <option <?= ($selectedOption != 'Panels' && $selectedOption != 'Stores') ? 'selected' : '' ?>><?= Yii::t('app/superadmin', 'db_helper.select.select_source') ?></option>
                                <option <?= $selectedOption == 'Panels' ? 'selected' : '' ?>><?= Yii::t('app/superadmin', 'db_helper.select.panels') ?></option>
                                <option <?= $selectedOption == 'Stores' ? 'selected' : '' ?>><?= Yii::t('app/superadmin', 'db_helper.select.stores') ?></option>
                            </select><br>
                            <?= Html::textarea('query', $query, ['class' => 'query_input form-control', 'rows' => '7']); ?>
                            <br>
                            <?= Html::submitButton(Yii::t('app/superadmin', 'Apply'), [
                                'class' => 'btn btn-outline btn-primary',
                                'name' => 'db-helper-button',
                                'id' => 'dbHelperButton'
                            ]) ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                        <div class="col-lg-6">
                            <pre class="query_content">
                                <?php foreach ($models as $model) : ?>
                                <?= $model ?>
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