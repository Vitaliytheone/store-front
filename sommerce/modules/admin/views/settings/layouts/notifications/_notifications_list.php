<?php
    /* @var $this \yii\web\View */
    /* @var $notifications array */

    use common\models\sommerces\NotificationDefaultTemplates;
    use sommerce\modules\admin\components\Url;
    use common\models\sommerce\NotificationTemplates;
    use yii\bootstrap\Html;
?>
<div class="row">
    <div class="col-md-3">
        <div class="settings-notification__category-title"><?= Yii::t('admin', 'notifications.customers_block_title') ?></div>
        <div class="settings-notification__category-description"><?= Yii::t('admin', 'notifications.customers_block_description') ?></div>
    </div>
    <div class="col-md-9">
        <div class="settings-notification__block">
            <?php foreach ($notifications[NotificationDefaultTemplates::RECIPIENT_CUSTOMER] as $notification) : ?>
                <div class="settings-notification__card">
                    <div class="settings-notification__card-header">
                        <div class="settings-notification__card-title">
                            <?= $notification['label'] ?>
                        </div>
                        <div class="settings-notification__card-description">
                            <?= $notification['description'] ?>
                        </div>
                    </div>
                    <div class="settings-notification__card-actions">
                        <span class="m-switch m-switch--outline m-switch--icon m-switch--primary switch-notification">
                            <label>
                                <?= Html::checkbox('notifications', $notification['status'], [
                                    'class' => 'language-checkbox change-status',
                                    'data-enable' => Url::toRoute(['/settings/change-notification-status', 'code' => $notification['code'], 'status' => NotificationTemplates::STATUS_ENABLED]),
                                    'data-disable' => Url::toRoute(['/settings/change-notification-status', 'code' => $notification['code'], 'status' => NotificationTemplates::STATUS_DISABLED]),
                                ])?>
                                <span></span>
                            </label>
                        </span>
                        <a href="<?= Url::toRoute(['/settings/edit-notification', 'code' => $notification['code']]) ?>" class="btn btn-sm m-btn--pill m-btn--air btn-primary">
                            <?= Yii::t('admin', 'notifications.edit_button') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="settings-notification__category-title"><?= Yii::t('admin', 'notifications.admin_block_title') ?></div>
        <div class="settings-notification__category-description"><?= Yii::t('admin', 'notifications.admin_block_description') ?></div>
    </div>
    <div class="col-md-9">
        <div class="settings-notification__block">
            <?php foreach ($notifications[NotificationDefaultTemplates::RECIPIENT_ADMIN] as $notification) : ?>
                <div class="settings-notification__card">
                    <div class="settings-notification__card-header">
                        <div class="settings-notification__card-title">
                            <?= $notification['label'] ?>
                        </div>
                        <div class="settings-notification__card-description">
                            <?= $notification['description'] ?>
                        </div>
                    </div>
                    <div class="settings-notification__card-actions">
                        <span class="m-switch m-switch--outline m-switch--icon m-switch--primary switch-notification">
                            <label>
                                 <?= Html::checkbox('notifications', $notification['status'], [
                                     'class' => 'language-checkbox change-status',
                                     'data-enable' => Url::toRoute(['/settings/change-notification-status', 'code' => $notification['code'], 'status' => NotificationTemplates::STATUS_ENABLED]),
                                     'data-disable' => Url::toRoute(['/settings/change-notification-status', 'code' => $notification['code'], 'status' => NotificationTemplates::STATUS_DISABLED]),
                                 ])?>
                                <span></span>
                            </label>
                        </span>
                        <a href="<?= Url::toRoute(['/settings/edit-notification', 'code' => $notification['code']]) ?>" class="btn btn-sm m-btn--pill m-btn--air btn-primary">
                            <?= Yii::t('admin', 'notifications.edit_button') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>