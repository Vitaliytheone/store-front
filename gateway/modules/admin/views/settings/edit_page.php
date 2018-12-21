<?php

use yii\helpers\Html;
use admin\components\Url;
use \sommerce\assets\PagesAsset;

/* @var $this \yii\web\View */
/* @var $pageForm \admin\models\forms\EditPageForm */
/* @var $isNewPage boolean Is page is new or updated */
/* @var $actionUrl string Action form url */
/* @var $siteUrl string */

$page = $pageForm->getPage();

PagesAsset::register($this);

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
                            <?= $isNewPage ? Yii::t('admin', 'settings.pages_create_page') : Yii::t('admin', 'settings.pages_edit_page') ?>
                        </h3>
                    </div>
                </div>
            </div>

            <?= Html::beginForm($actionUrl, 'POST', [
                'id' => 'pageForm',
                'class' => 'form-horizontal',
                'role' => 'form',
                'data-new_page' => $isNewPage
            ]) ?>
                <div class="modal-loader square hidden"></div>


                <div class="m-content">

                    <div class="error-summary alert alert-danger hidden"></div>

                    <div class="form-group">
                        <label for="edit-page-title"><?= Yii::t('admin', 'settings.pages_title') ?></label>
                        <?= Html::activeTextInput($pageForm, 'title', [
                            'class' => 'form-control form_field__name',
                            'id' => 'edit-page-title',
                        ])?>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlSelect1"><?= Yii::t('admin', 'settings.pages_visibility') ?></label>

                        <?= Html::activeDropDownList($pageForm, 'visibility', $pageForm->getVisibilityList(), [
                            'class' => 'form-control form_field__visibility',
                            'id' => 'exampleFormControlSelect1',
                        ])?>
                    </div>

                    <div class="form-group">
                        <?= Html::activeTextarea($pageForm, 'content', [
                            'class' => 'form-control',
                            'id' => 'description',
                            'title' => 'Description',
                            'rows' => 20
                        ])?>
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
                                <div class="seo-preview__url"><?= $siteUrl; ?>/<span class="edit-seo__url"></span></div>
                                <div class="seo-preview__description edit-seo__meta"></div>
                            </div>

                            <div class="collapse" id="seo-block">
                                <div class="form-group">
                                    <label for="edit-seo__title"><?= Yii::t('admin', 'settings.pages_seo_page') ?></label>
                                    <?= Html::activeTextInput($pageForm, 'seo_title', [
                                        'class' => 'form-control form_field__seo_title',
                                        'id' => 'edit-seo__title',
                                    ])?>
                                    <small class="form-text text-muted">
                                        <span class="edit-seo__title-muted"></span> <?= Yii::t('admin', 'settings.pages_seo_page_chars_used') ?>
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__meta"><?= Yii::t('admin', 'settings.pages_seo_meta') ?></label>
                                    <?= Html::activeTextarea($pageForm, 'seo_description', [
                                        'class' => 'form-control form_field__seo_description',
                                        'id' => 'edit-seo__meta',
                                        'rows' => 3,
                                    ])?>
                                    <small class="form-text text-muted">
                                        <span class="edit-seo__meta-muted"></span> <?= Yii::t('admin', 'settings.pages_seo_meta_chars_used') ?>
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__meta-keyword"><?= Yii::t('admin', 'settings.pages_seo_meta_keywords') ?></label>
                                    <?= Html::activeTextarea($pageForm, 'seo_keywords', [
                                        'class' => 'form-control',
                                        'id' => 'edit-seo__meta-keyword',
                                        'rows' => 3,
                                    ])?>
                                </div>
                                <div class="form-group">
                                    <label for="edit-seo__url"><?= Yii::t('admin', 'settings.pages_seo_url') ?></label>
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3"><?= $siteUrl ?>/</span>
                                        <?= Html::activeTextInput($pageForm, 'url', [
                                            'class' => 'form-control form_field__url',
                                            'id' => 'edit-seo__url',
                                        ])?>
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
        </div>

    </div>
</div>
<!-- end::Body -->