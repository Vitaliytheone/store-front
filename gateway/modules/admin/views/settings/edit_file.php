<?php

use yii\helpers\Html;
use gateway\assets\ThemesAsset;
use admin\widgets\FilesTreeWidget;
use admin\components\Url;
use common\models\gateway\Files;
use admin\models\forms\EditFileForm;
use common\components\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this \yii\web\View */
/* @var $file Files */
/** @var $model EditFileForm */
/** @var $files array */

ThemesAsset::register($this);
?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-subheader">

        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="row">
                        <div class="col-12">

                            <?php $form = ActiveForm::begin([
                                'id' => 'editThemeForm',
                                'action' => Url::toRoute(['/settings/update-file', 'id' => ArrayHelper::getValue($file, 'id')]),
                                'options' => [
                                    'class' => "form",
                                ],
                                'fieldClass' => 'yii\bootstrap\ActiveField',
                                'fieldConfig' => [
                                    'template' => "{label}\n{input}",
                                ],
                            ]); ?>

                                <div class="modal-loader square hidden"></div>

                                <div class="m-portlet gateway-settings__editor">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-caption">
                                            <div class="m-portlet__head-title">
                                                <h3 class="m-portlet__head-text">
                                                    <?php if ($file): ?>
                                                        <small>
                                                            <?= $file->name ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row gateway-editorPage <?= $file ? '' : 'align-items-center' ?> ">

                                        <div class="col-lg-10 gateway-editorPage__text order-2 order-lg-1 <?= $file ? '' : 'text-center' ?> ">
                                            <?php if ($file): ?>
                                                <div class="gateway-editorPage__codemirror">
                                                    <?= Html::activeTextarea($model, 'content', ['id' => 'code'])?>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-12">
                                                    <div class="gateway-editorPage__preview-icon"></div>
                                                    <span><?= Yii::t('admin', 'settings.files_start_editing') ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-lg-2 gateway-editorPage__list order-1 order-lg-2">

                                            <?= FilesTreeWidget::widget([
                                                'file' => $file,
                                                'files' => $files,
                                            ]) ?>

                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="gateway-editorPage__actions">
                                            <?php if ($file && Files::can(Files::CAN_UPDATE, $file)): ?>
                                                <button type="submit" class="btn btn-success m-btn--air"><?= Yii::t('admin', 'settings.files_editing_save') ?></button>
                                                <a href="<?= Url::toRoute('/settings/files') ?>" class="btn btn-secondary m-btn--air"><?= Yii::t('admin', 'settings.files_editing_cancel') ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>