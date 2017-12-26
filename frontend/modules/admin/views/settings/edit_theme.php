<?php
use yii\helpers\Html;
use frontend\assets\ThemesAsset;
use frontend\modules\admin\widgets\FilesTree;
use frontend\modules\admin\components\Url;

/* @var $this \yii\web\View */
/* @var $filesTree array */
/** @var $theme \common\models\stores\DefaultThemes | \common\models\store\CustomThemes */
/** @var $currentFile */

ThemesAsset::register($this);
?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-subheader">

        <div class="container-fluid">
            <div class="row">

                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="m-portlet sommerce-settings__editor">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <h3 class="m-portlet__head-text">
                                                <?= $theme->name ?>
                                                <?php if($currentFile): ?>
                                                    <small>
                                                        <?= $currentFile ?>
                                                    </small>
                                                <?php endif; ?>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="row sommerce-editorPage <?= $currentFile ? '' : 'align-items-center'?> ">

                                    <div class="col-lg-10 sommerce-editorPage__text order-2 order-lg-1 <?= $currentFile ? '' : 'text-center'?> ">
                                        <?php if($currentFile): ?>
                                            <div class="sommerce-editorPage__codemirror">
                                                <textarea class="codemirror-textarea" id="codemirror"></textarea>
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
                                            'filesTree' => $filesTree,
                                            'currentFile' => $currentFile,
                                            'themePath' => $theme->getThemePath(),
                                        ]) ?>

                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="sommerce-editorPage__actions">
                                        <button class="btn btn-success m-btn--air"><?= Yii::t('admin', 'settings.themes_editing_save') ?></button>
                                        <button class="btn btn-secondary m-btn--air"><?= Yii::t('admin', 'settings.themes_editing_cancel') ?></button>
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>