<?php
    /* @var $this \yii\web\View */
?>
<div class="modal fade" tabindex="-1" role="dialog" id="notificationPreviewModal">
    <div class="modal-dialog modal-lg modal-lg-preview" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'settings.notification_preview_m_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <iframe src="" frameborder="0" class="notification-preview__iframe"></iframe>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('admin', 'settings.notification_preview_m_cancel'); ?></button>
            </div>
        </div>
    </div>
</div>