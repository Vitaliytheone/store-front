<?php
    /* @var $this \yii\web\View */
    /* @var $notifications array */
    /* @var $emails array */
?>
<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'notifications'
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
                            <?= Yii::t('admin', 'settings.notifications_page_title') ?>
                        </h3>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">

                <div class="settings-notification">
                    <?= $this->render('layouts/notifications/_notifications_list', ['notifications' => $notifications]) ?>

                    <?= $this->render('layouts/notifications/_emails_list', ['emails' => $emails]) ?>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- end::Body -->

<?= $this->render('layouts/notifications/_edit_admin_email_modal') ?>
<?= $this->render('layouts/notifications/_delete_admin_email_modal') ?>