<?php
    /* @var $this yii\web\View */
?>
<div class="modal fade" id="paymentRefundModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'payments.list.refund_modal_title')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body max-height-400">
                <h5 class="text-danger"><?= Yii::t('app/superadmin', 'payments.list.refund_modal_text_1') ?></h5>
                <p>
                    <span><?= Yii::t('app/superadmin', 'payments.list.refund_modal_text_2') ?></span> <br>
                    <span><?= Yii::t('app/superadmin', 'payments.list.refund_modal_text_3') ?></span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'payments.list.refund_modal_btn_cancel')?></button>
                <a href="#" id="submitRefund" class="btn btn-danger"><?= Yii::t('app/superadmin', 'payments.list.refund_modal_btn_submit')?></a>
            </div>
        </div>
    </div>
</div>