<?php
/* @var $this \yii\web\View */

/* @var $themes array */

use frontend\modules\admin\components\Url;

?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">

        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'themes'
            ]) ?>
        </div>

        <div class="m-grid__item m-grid__item--fluid m-wrapper">

            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.themes_page_title') ?>
                        </h3>
                    </div>
                    <div>
                        <div class="m-dropdown--align-right">
                            <a href="<?= Url::toRoute('/settings/create-theme') ?>"
                               class="btn btn-primary m-btn--air btn-brand btn-primary"><?= Yii::t('admin', 'settings.themes_add') ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="m-content">
                <div class="row">
                    <?php foreach ($themes as $theme): ?>

                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <div class="sommerce-settings_theme-cards <?= $theme['active'] ? 'active-theme' : '' ?> ">
                                <div class="sommerce-settings_cards-preview">
                                    <div class="sommerce-settings_theme-live">
                                        <div>
                                            <?php if ($theme['active']): ?>
                                                <a href="<?= Url::toRoute('/settings/edit-theme') ?>"><?= Yii::t('admin', 'settings.themes_edit_code') ?></a>
                                                <a href="http://front.sommerce.net/sommerce_themes/classic/dist/"><?= Yii::t('admin', 'settings.themes_customize') ?></a>
                                            <?php else: ?>
                                                <a href="<?= Url::toRoute(['/settings/activate-theme', 'id' => $theme['id']]) ?>"><?= Yii::t('admin', 'settings.themes_activate') ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <img src="<?= $theme['thumbnail'] ?>" alt="" class="img-fluid">
                                </div>
                                <div class="sommerce-settings_cards-title">
                                    <?php if ($theme['active']): ?>
                                        <strong><?= Yii::t('admin', 'settings.themes_active') ?></strong>
                                    <?php endif; ?>
                                    <?= $theme['name'] ?>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </div>

        </div>
    </div>
</div>