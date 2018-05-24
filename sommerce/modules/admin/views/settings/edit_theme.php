<?php

use yii\helpers\Html;
use sommerce\assets\ThemesAsset;
use sommerce\modules\admin\widgets\FilesTree;
use sommerce\modules\admin\components\Url;

/* @var $this \yii\web\View */
/* @var $filesTree array */
/** @var $theme \common\models\stores\DefaultThemes | \common\models\store\CustomThemes */
/** @var string $currentFile */
/** @var string $editDate */
/** @var string $currentFileContent */
/** @var bool $reset */


ThemesAsset::register($this);
?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-subheader">

        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="row">
                        <div class="col-12">

                            <form id="edit_theme_form" name="ThemeForm" method="post" action="<?= Url::toRoute(['/settings/update-theme', 'theme' => $theme->folder, 'file' => $currentFile]) ?>">

                                <div class="modal-loader square hidden"></div>

                                <?= Html::beginForm() ?>
                                <div class="m-portlet sommerce-settings__editor">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-caption">
                                            <div class="m-portlet__head-title">
                                                <h3 class="m-portlet__head-text">
                                                    <?= $theme->name ?>
                                                    <?php if ($currentFile): ?>
                                                        <small>
                                                            <?= $currentFile ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </h3>
                                            </div>
                                        </div>

                                        <div id="reset_file" class="m-portlet__head-tools <?= $reset ? '' : 'd-none' ?>">
                                            <ul class="m-portlet__nav">
                                                <li class="m-portlet__nav-item">
                                                    <a href="<?= Url::toRoute(['/settings/reset-theme-file', 'theme' => $theme->folder, 'file' => $currentFile]) ?>" class="m-portlet__nav-link m-portlet__nav-link--icon reset-file" data-toggle="modal" data-target="#modal_submit_reset">
                                                        <i class="la la-refresh"></i> <?= Yii::t('admin', 'settings.themes_editing_reset') ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="row sommerce-editorPage <?= $currentFile ? '' : 'align-items-center' ?> ">

                                        <div class="col-lg-10 sommerce-editorPage__text order-2 order-lg-1 <?= $currentFile ? '' : 'text-center' ?> ">
                                            <?php if ($currentFile): ?>
                                                <div class="sommerce-editorPage__codemirror">
                                                    <textarea class="codemirror-textarea" id="code" name="ThemeForm[file_content]"><?= Html::encode($currentFileContent) ?></textarea>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-12">
                                                    <div class="sommerce-editorPage__preview-icon"></div>
                                                    <span><?= Yii::t('admin', 'settings.themes_start_editing') ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-lg-2 sommerce-editorPage__list order-1 order-lg-2">

                                            <?= FilesTree::widget([
                                                'currentFile' => $currentFile,
                                                'themeFolder' => $theme->folder,
                                                'filesTree' => $filesTree,
                                            ]) ?>

                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="sommerce-editorPage__actions">

                                            <?php if ($currentFile): ?>
                                                <button type="submit" class="btn btn-success m-btn--air"><?= Yii::t('admin', 'settings.themes_editing_save') ?></button>
                                            <?php endif; ?>

                                            <a href="<?= Url::toRoute('/settings/themes') ?>" class="btn btn-secondary m-btn--air" data-toggle="modal" data-target="#modal_submit_close"><?= Yii::t('admin', 'settings.themes_editing_cancel') ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?= Html::endForm() ?>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_submit_close" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <div class="modal-loader hidden"></div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-warning"></span>
                        <p><?= Yii::t('admin', 'settings.themes_modal_submit_close_message')?></p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'settings.themes_modal_cancel') ?></button>
                        <a href="" class="submit_button btn btn-danger m-btn--air"><?= Yii::t('admin', 'settings.themes_modal_submit_close_submit')?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_submit_reset" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <div class="modal-loader hidden"></div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-warning"></span>
                        <p><?= Yii::t('admin', 'settings.themes_modal_submit_reset_message')?></p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'settings.themes_modal_cancel') ?></button>
                        <a href="" class="submit_button btn btn-danger m-btn--air"><?= Yii::t('admin', 'settings.themes_modal_submit_reset_submit')?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>