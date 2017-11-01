<?php
    /* @var $this \yii\web\View */

    use frontend\modules\admin\components\Url;
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
                'active' => 'pages'
            ])?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            Pages
                        </h3>
                    </div>
                    <div>
                        <div class="m-dropdown--align-right">
                            <a href="<?= Url::toRoute('/settings/add-page') ?>" class="btn btn-primary  m-btn--air btn-brand cursor-pointer">Add page</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- END: Subheader -->
            <div class="m-content">
                <table class="table-sommerce table m-table m-table--head-no-border">
                    <thead>
                    <tr>
                        <th>Page name</th>
                        <th>Updated</th>
                        <th>Visibility</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="sommerce-table__no-wrap">Contact us</td>
                        <td class="sommerce-table__no-wrap">2017-04-28 13:32:26</td>
                        <td>Visible</td>
                        <td class="sommerce-table__no-wrap text-lg-right">
                            <a class="btn m-btn--pill m-btn--air btn-sm btn-primary" href="<?= Url::toRoute('/settings/edit-page') ?>">
                                Edit
                            </a>
                            <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" title="Delete">
                                <i class="la la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="sommerce-table__no-wrap">Terms of Service</td>
                        <td class="sommerce-table__no-wrap">2017-04-28 13:32:26</td>
                        <td>Visible</td>
                        <td class="sommerce-table__no-wrap text-lg-right">
                            <a class="btn m-btn--pill m-btn--air btn-sm        btn-primary" href="<?= Url::toRoute('/settings/edit-page') ?>">
                                Edit
                            </a>
                            <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" title="Delete">
                                <i class="la la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="sommerce-table__no-wrap">Refund policy</td>
                        <td class="sommerce-table__no-wrap">2017-04-28 13:32:26</td>
                        <td>Visible</td>
                        <td class="sommerce-table__no-wrap text-lg-right">
                            <a class="btn m-btn--pill m-btn--air btn-sm        btn-primary" href="<?= Url::toRoute('/settings/edit-page') ?>">
                                Edit
                            </a>
                            <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" title="Delete">
                                <i class="la la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<!-- end::Body -->