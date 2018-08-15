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
                                'id' => 'dbHelperForm',
                                'action' => Url::toRoute('/tools/apply-query'),
                                'options' => [
                                    'class' => "form",
                                ],
                                'fieldClass' => 'yii\bootstrap\ActiveField',
                                'fieldConfig' => [
                                    'template' => "{label}\n{input}",
                                ],
                            ]) ?>

                            <select class="form-control db_name">
                                <option></option>
                                <?php foreach ($models['panels'] as $panel) : ?>
                                    <option><?= $panel['panel'] ?></span></option>
                                <?php endforeach; ?>
                                <?php foreach ($models['stores'] as $store) : ?>
                                    <option><?= $store['store'] ?></option>
                                <?php endforeach; ?>
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
                            <pre class="query_content">UPDATE `db_name`.`services` SET `provider_id` = `res`, `provider_service_id` = `reid`, `provider_service_params` = `params`;
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>