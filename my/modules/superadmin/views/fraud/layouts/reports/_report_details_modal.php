<?php
/* @var $this yii\web\View */
?>
<div class="modal fade" id="reportsDetailsModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'fraud_reports.list.reports_modal_header')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body max-height-400"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'fraud_reports.list.reports_modal_btn_close')?></button>
            </div>
        </div>
    </div>
</div>