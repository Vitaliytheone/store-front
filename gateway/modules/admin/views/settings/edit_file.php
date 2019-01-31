<?php

use yii\helpers\Html;
use gateway\assets\FilesAsset;
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

FilesAsset::register($this);
?>

<!-- Page Content Start -->
<div class="page-container">
    <div class="m-subheader">

        <div class="container-fluid">
            <div class="row">

                <div class="col-12 mt-5">
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

                                <div class="m-portlet sommerce-settings__editor">
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

                                        <div class="m-portlet__head-tools">
                                            <?php if ($file && Files::can(Files::CAN_UPDATE, $file)) : ?>
                                                <button type="submit" class="btn btn-success m-btn--air"><?= Yii::t('admin', 'settings.files_editing_save') ?></button>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="row sommerce-editorPage">
                                        <div class="col-lg-10 sommerce-editorPage__text order-2 order-lg-1">

                                            <?php if ($file) : ?>
                                                <?php if (Files::can(Files::CAN_UPDATE, $file)) : ?>
                                                    <div class="sommerce-editorPage__codemirror">
                                                        <?= Html::activeTextarea($model, 'content', ['id' => 'code'])?>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (Files::can(Files::CAN_PREVIEW, $file)) : ?>
                                                    <div class="sommerce-editorPage__image-preview">
                                                        <?= Html::img(Url::toRoute(['/settings/preview-file', 'id' => ArrayHelper::getValue($file, 'id')])) ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <div class="col-12 text-center">
                                                    <div class="sommerce-editorPage__preview-icon"></div>
                                                    <span><?= Yii::t('admin', 'settings.files_start_editing') ?></span>
                                                </div>
                                            <?php endif; ?>

                                        </div>

                                        <div class="col-lg-2 sommerce-editorPage__list order-1 order-lg-2">

                                            <?= FilesTreeWidget::widget([
                                                'file' => $file,
                                                'files' => $files,
                                            ]) ?>
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

<?= $this->render('layouts/files/_rename_file'); ?>
<?= $this->render('layouts/files/_create_file'); ?>
<?= $this->render('layouts/files/_upload_file'); ?>