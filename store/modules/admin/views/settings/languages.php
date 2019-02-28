<?php

use store\modules\admin\components\Url;

/* @var $storeLanguages array */
/* @var $availableLanguages array */
/* @var $this \yii\web\View */

?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'languages'
            ])?>
            <!-- END: Aside Menu -->
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.languages_page_title') ?>
                        </h3>
                    </div>
                    <div>
                        <div class="m-dropdown--align-right">
                            <a href="#" class="btn btn-primary m-btn--air btn-brand cursor-pointer" data-toggle="modal" data-target=".add-language-modal"><?= Yii::t('admin', 'settings.languages_add') ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">

                <div class="settings-language__block">
                    <?php foreach ($storeLanguages as $code => $language): ?>
                        <div class="settings-language__block-card">
                            <div class="settings-language__block-title">
                                <?= $language['name'] ?>
                            </div>
                            <div class="settings-language__block-actions">
                                <span class="m-switch m-switch--outline m-switch--icon m-switch--primary switch-language">
									<label>
										<input type="radio" <?php if($language['active']): ?>checked<?php endif; ?> name="language" value="<?= $code ?>" class="language-checkbox">
										<span></span>
									</label>
								</span>
                                <a href="<?= Url::toRoute(['/settings/edit-language', 'code' => $code]) ?>" class="btn btn-sm m-btn--pill m-btn--air btn-primary">
                                    <?= Yii::t('admin', 'settings.languages_edit') ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->render('layouts/languages/_modal_add_language', ['availableLanguages' => $availableLanguages]) ?>
