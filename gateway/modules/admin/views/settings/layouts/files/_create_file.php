<?php
/* @var $this yii\web\View */
/* @var $form \gateway\components\ActiveForm */
/* @var $modal admin\models\forms\CreateFileForm */

use admin\models\forms\CreateFileForm;
use gateway\components\ActiveForm;
use my\helpers\Url;
use yii\bootstrap\Html;

$model = new CreateFileForm();
?>

<div class="modal fade" id="createFileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'id' => 'createFileForm',
                'action' => Url::toRoute("/settings/create-file"),
                'options' => [
                    'class' => "modal-content",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>
            <div class="modal-body">
                <ul class="nav nav-tabs  m-tabs-line" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_1_1" role="tab"><?= Yii::t('admin', 'settings.files.create_file.tab.create_file') ?></a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_1_2" role="tab"><?= Yii::t('admin', 'settings.files.create_file.tab.upload_file') ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="m_tabs_1_1" role="tabpanel">
                        <?= $form->field($model, 'name') ?>

                        <div class="text-right">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"><?= Yii::t('admin', 'settings.files.create_file.cancel') ?></button>
                            <?= Html::submitButton(Yii::t('admin', 'settings.files.create_file.save'), [
                                'class' => 'btn btn-primary',
                                'name' => 'create-file-button',
                                'id' => 'createFileButton'
                            ]) ?>
                        </div>
                    </div>
                    <div class="tab-pane" id="m_tabs_1_2" role="tabpanel">
                        <div class="mb-4">
                            <?= $form->field($model, 'file')->fileInput() ?>
                        </div>

                        <div class="text-right">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"><?= Yii::t('admin', 'settings.files.create_file.cancel') ?></button>
                            <?= Html::submitButton(Yii::t('admin', 'settings.files.create_file.upload'), [
                                'class' => 'btn btn-primary',
                                'name' => 'create-file-button',
                                'id' => 'createFileButton'
                            ]) ?>
                        </div>
                    </div>
                </div>
                <?= Html::activeHiddenInput($model, 'type') ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>