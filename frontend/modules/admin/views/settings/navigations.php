<?php

use \frontend\assets\NavigationsAsset;
use frontend\modules\admin\components\Url;
use \common\models\store\Navigations;

/* @var $this \yii\web\View */

$linkTypes = Navigations::getLinkNames();

NavigationsAsset::register($this);

?>

<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'navigations'
            ])?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.nav_page_title')?>
                        </h3>
                    </div>

                    <div>
                        <div class="m-dropdown--align-right">
                            <button class="btn btn-primary  m-btn--air btn-brand cursor-pointer" data-toggle="modal" data-target=".edit_navigation" data-backdrop="static">Add menu item</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">
                <div class="dd" id="nestable">
                    <ol class="dd-list">

                        <li class="dd-item" data-id="1">
                            <div class="dd-handle">YouTube 1</div>
                            <div class="dd-edit-button">
                                <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                    Edit
                                </a>
                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                    <i class="la la-trash"></i>
                                </a>
                            </div>
                        </li>

                        <li class="dd-item" data-id="2">
                            <div class="dd-handle">YouTube 2</div>
                            <div class="dd-edit-button">
                                <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm"  data-toggle="modal" data-target=".edit_navigation">
                                    Edit
                                </a>
                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="Delete">
                                    <i class="la la-trash"></i>
                                </a>
                            </div>
                        </li>

                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end::Body -->

<?= $this->render('layouts/navigations/_modal_delete') ?>
<?= $this->render('layouts/navigations/_modal_menu_item_form', [
        'linkTypes' => $linkTypes,
]) ?>
