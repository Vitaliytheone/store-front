<?php

use frontend\assets\NavigationsAsset;
use frontend\modules\admin\components\Url;
use frontend\helpers\NavigationHelper;

/* @var $this \yii\web\View */
/* @var $linkTypes array */
/* @var $navTree array */

NavigationsAsset::register($this);

?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver m-container m-container--responsive m-container--xxl m-page__container">

        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'navigations'
            ])?>
        </div>

        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.nav_page_title')?>
                        </h3>
                    </div>

                    <div>
                        <div class="m-dropdown--align-right">
                            <button class="btn btn-primary  m-btn--air btn-brand cursor-pointer" data-submit_url="<?= Url::toRoute(['/settings/create-nav'])?>" data-toggle="modal" data-target=".edit_navigation" data-backdrop="static">
                                <?= Yii::t('admin', 'settings.nav_bt_add')?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="m-content">
                <div class="dd" id="nestable">
                    <ol class="dd-list">
                        <?= NavigationHelper::menuTree($navTree) ?>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->render('layouts/navigations/_modal_delete') ?>
<?= $this->render('layouts/navigations/_modal_menu_item_form', [
        'linkTypes' => $linkTypes,
]) ?>
