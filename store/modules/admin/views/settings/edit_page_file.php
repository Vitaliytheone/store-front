<?php

use yii\helpers\Html;
use store\modules\admin\components\Url;
use \store\assets\PagesAsset;

/* @var $this \yii\web\View */
/* @var $pageForm \store\modules\admin\models\forms\EditPageForm */
/* @var $isNewPage boolean Is page is new or updated */
/* @var $actionUrl string Action form url */
/* @var $storeUrl string */

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

            <form id="pageForm" class="form-horizontal" action="<?= $actionUrl ?>" method="post" role="form" data-new_page="<?= $isNewPage ?>">

                <div class="modal-loader square hidden"></div>

                <?= Html::beginForm() ?>
                <div class="m-content">

                    <div class="error-summary alert alert-danger hidden"></div>

                    <div class="form-group">
                        <textarea class="form-control"
                                  rows="20"
                                  id="description"
                                  title="Description"
                                  name="PageForm[content]"><?= $page->content ?></textarea>
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