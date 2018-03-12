<?php

use yii\helpers\Html;
use frontend\modules\admin\components\Url;
use common\components\ActiveForm;
use \frontend\assets\PagesAsset;

/* @var $page \frontend\modules\admin\models\forms\EditPageForm */
/* @var $storeUrl string */
/* @var $this \yii\web\View */

PagesAsset::register($this);

$actionUrl = $page->isNewRecord ? Url::toRoute('/settings/create-page') :  Url::toRoute(['/settings/edit-page', 'id' => $page->id]);

?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">

        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'pages'
            ]) ?>
        </div>

        <div class="m-grid__item m-grid__item--fluid m-wrapper">

            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= $page->isNewRecord ? Yii::t('admin', 'settings.pages_create_page') : Yii::t('admin', 'settings.pages_edit_page') ?>
                        </h3>
                    </div>
                </div>
            </div>

            <form id="pageForm" class="form-horizontal" action="<?= $actionUrl ?>" method="post" role="form" data-new_page="<?= $page->isNewRecord ?>">
            <?= Html::beginForm() ?>
                <div class="m-content">

                    <?php if($page->hasErrors()): ?>
                        <div class="error-summary alert alert-danger"><?= ActiveForm::firstError($page) ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="edit-page-title"><?= Yii::t('admin', 'settings.pages_title') ?></label>
                        <input type="text" class="form-control form_field__name" id="edit-page-title" name="PageForm[title]" value="<?= $page->title ?>">
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlSelect1"><?= Yii::t('admin', 'settings.pages_visibility') ?></label>
                        <select class="form-control form_field__visibility" id="exampleFormControlSelect1" name="PageForm[visibility]">
                            <option name="PageForm[visibility]" value="1" <?= $page->visibility == 1 ? 'selected' : '' ?>>
                                <?= Yii::t('admin', 'settings.pages_visibility_visible') ?>
                            </option>
                            <option name="PageForm[visibility]" value="0" <?= $page->visibility == 0 ? 'selected' : '' ?>>
                                <?= Yii::t('admin', 'settings.pages_visibility_hidden') ?>
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <textarea class="summernote form_field__content d-none" id="description" title="Description" name="PageForm[content]"><?= $page->content ?></textarea>
                    </div>

                    <div class="card card-white">
                        <div class="card-body">

                            <div class="row seo-header align-items-center">
                                <div class="col-sm-8">
                                    <?= Yii::t('admin', 'settings.pages_seo_preview') ?>
                                </div>
                                <div class="col-sm-4 text-sm-right">
                                    <a class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">
                                        <?= Yii::t('admin', 'settings.pages_seo_edit') ?>
                                    </a>
                                </div>
                            </div>

                            <div class="seo-preview">
                                <div class="seo-preview__title edit-seo__title"></div>
                                <div class="seo-preview__url"><?= $storeUrl; ?>/<span class="edit-seo__url"></span></div>
                                <div class="seo-preview__description edit-seo__meta"></div>
                            </div>

                            <div class="collapse" id="seo-block">
                                <div class="form-group">
                                    <label for="edit-seo__title"><?= Yii::t('admin', 'settings.pages_seo_page') ?></label>
                                    <input class="form-control form_field__seo_title" id="edit-seo__title" name="PageForm[seo_title]" value="<?= $page->seo_title ?>">
                                    <small class="form-text text-muted">
                                        <span class="edit-seo__title-muted"></span> <?= Yii::t('admin', 'settings.pages_seo_page_chars_used') ?>
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__meta"><?= Yii::t('admin', 'settings.pages_seo_meta') ?></label>
                                    <textarea class="form-control form_field__seo_description" id="edit-seo__meta" rows="3" name="PageForm[seo_description]" ><?= $page->seo_description ?></textarea>
                                    <small class="form-text text-muted">
                                        <span class="edit-seo__meta-muted"></span> <?= Yii::t('admin', 'settings.pages_seo_meta_chars_used') ?>
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__meta-keyword"><?= Yii::t('admin', 'settings.pages_seo_meta_keywords') ?></label>
                                    <textarea class="form-control" id="edit-seo__meta-keyword" rows="3" name="PageForm[seo_keywords]"><?= $page->seo_keywords ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__url"><?= Yii::t('admin', 'settings.pages_seo_url') ?></label>
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3"><?= $storeUrl ?>/</span>
                                        <input type="text" class="form-control form_field__url" id="edit-seo__url" name="PageForm[url]" value="<?= $page->url ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <button type="submit" class="btn btn-primary"><?= Yii::t('admin', 'settings.pages_save') ?></button>
                    <a class="btn btn-secondary" href="<?= Url::toRoute('/settings/pages') ?>"><?= Yii::t('admin', 'settings.pages_cancel') ?></a>
                </div>
            <?= Html::endForm() ?>
            </form>

        </div>

    </div>
</div>
<!-- end::Body -->