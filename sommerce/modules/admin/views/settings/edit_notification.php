<?php
    /* @var $this \yii\web\View */
    /* @var $model \sommerce\modules\admin\models\forms\EditNotificationForm */

    use sommerce\modules\admin\components\Url;
    use sommerce\assets\CodemirrorAsset;
    use common\components\ActiveForm;
    use yii\bootstrap\Html;

    CodemirrorAsset::register($this);
?>
<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <!-- BEGIN: Aside Menu -->
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
                            <?= $this->title ?>
                        </h3>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">
                <?php $form = ActiveForm::begin([
                    'id' => 'edit-notification-form',
                    'fieldConfig' => [
                        'template' => "{input}",
                    ],
                ]); ?>
                    <?= $form->errorSummary($model, [
                        'id' => 'editNotificationError'
                    ]); ?>
                    <div class="m-form mb-3">
                        <div class="form-group m-form__group">
                            <?= Html::activeLabel($model, 'subject')?>
                            <?= $form->field($model, 'subject')->textInput([
                                'autofocus' => true,
                                'class' => 'form-control m-input'
                            ]) ?>
                        </div>

                        <div class="form-group m-form__group">
                            <?= Html::activeLabel($model, 'body')?>
                            <div class="settings-notification__editor">

                                <style>
                                    .CodeMirror {
                                        height: 550px!important;
                                    }
                                </style>
                                <?= $form->field($model, 'body')->textarea([
                                    'id' => 'code'
                                ]) ?>
                            </div>


                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-7">
                            <div class="btn-group m-btn-group">
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target=".notification-preview"><?= Yii::t('admin', 'settings.edit_notification_preview_btn')?></button>
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target=".notification-test-send"><?= Yii::t('admin', 'settings.edit_notification_send_test_btn')?></button>
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target=".notification-reset"><?= Yii::t('admin', 'settings.edit_notification_reset_btn')?></button>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-right">
                            <a href="<?= Url::toRoute(['/settings/notifications']) ?>" class="btn btn-secondary"><?= Yii::t('admin', 'settings.edit_notification_cancel_btn')?></a>
                            <button type="submit" class="btn btn-success ml-2"><?= Yii::t('admin', 'settings.edit_notification_submit_btn')?></button>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<!-- end::Body -->

<?= $this->render('layouts/notifications/_notification_preview_modal') ?>
<?= $this->render('layouts/notifications/_send_test_modal') ?>
<?= $this->render('layouts/notifications/_reset_notification_modal') ?>

