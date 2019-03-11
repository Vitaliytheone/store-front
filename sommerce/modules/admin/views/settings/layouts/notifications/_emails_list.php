<?php
    /* @var $this \yii\web\View */
    /* @var $emails array */

    use yii\bootstrap\Html;
    use sommerce\modules\admin\components\Url;
    use common\models\sommerce\NotificationAdminEmails;
?>
<div class="row">
    <div class="col-md-3">
        <div class="settings-notification__admin-title">
            <?= Yii::t('admin', 'emails.block_title') ?>
        </div>
        <div class="settings-notification__admin-description">
            <?= Yii::t('admin', 'emails.block_description') ?>
        </div>
    </div>
    <div class="col-md-9">

        <div class="settings-notification__admin-blocks">

            <div class="settings-notification__admin-blocks-cards">
                <?php foreach ($emails as $email) : ?>
                    <div class="settings-notification__admin-card">
                        <div class="settings-notification__admin-card-title">
                            <?= $email['email'] ?>
                            <?php if ($email['primary']) : ?>
                                <span class="m-badge m-badge--metal m-badge--wide text-white"><?= Yii::t('admin', 'emails.primary_label') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="settings-notification__admin-card-actions">
                            <span class="m-switch m-switch--outline m-switch--icon m-switch--primary switch-notification-admin">
                                <label>
                                    <?= Html::checkbox('notifications', $email['status'], [
                                        'class' => 'language-checkbox change-status',
                                        'data-enable' => Url::toRoute(['/settings/change-email-status', 'id' => $email['id'], 'status' => NotificationAdminEmails::STATUS_ENABLED]),
                                        'data-disable' => Url::toRoute(['/settings/change-email-status', 'id' => $email['id'], 'status' => NotificationAdminEmails::STATUS_DISABLED]),
                                    ])?>
                                    <span></span>
                                </label>
                            </span>
                            <?= Html::a(Yii::t('admin', 'emails.edit_button'), Url::toRoute(['/settings/edit-email', 'id' => $email['id']]), [
                                'class' => 'btn btn-sm m-btn--pill m-btn--air btn-primary edit-email',
                                'data-header' => Yii::t('admin', 'settings.emails_m_edit_header'),
                                'data-email' => $email['email']
                            ])?>
                            <?php if (!$email['primary']) : ?>
                                <?= Html::a('<i class="la la-trash"></i>', Url::toRoute(['/settings/delete-email', 'id' => $email['id']]), [
                                    'class' => 'delete-email m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill',
                                ])?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="settings-notification__admin-action">
                <?= Html::a(Yii::t('admin', 'emails.add_button'), Url::toRoute(['/settings/create-email']), [
                    'class' => 'btn btn-sm m-btn--air btn-primary mr-2 create-email',
                    'data-header' => Yii::t('admin', 'settings.emails_m_create_header')
                ])?>
            </div>
        </div>
    </div>
</div>