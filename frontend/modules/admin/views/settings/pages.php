<?php

use frontend\modules\admin\components\Url;

/* @var $pages array */
/* @var $this \yii\web\View */

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
                            <?= Yii::t('admin', 'settings.pages_page_title') ?>
                        </h3>
                    </div>
                    <div>
                        <div class="m-dropdown--align-right">
                            <a href="<?= Url::toRoute('/settings/create-page') ?>"
                               class="btn btn-primary  m-btn--air btn-brand cursor-pointer">
                                <?= Yii::t('admin', 'settings.pages_add') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="m-content">
                <table class="table-sommerce table m-table m-table--head-no-border">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Page name</th>
                        <th>Updated</th>
                        <th>Visibility</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pages as $page):?>
                        <?= $this->render('layouts/pages/_page_item', ['page' => $page]) ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?= $this->render('layouts/pages/_modal_delete_page') ?>
