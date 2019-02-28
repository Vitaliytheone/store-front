<div class="modal fade order_modal_alert" id="modal-alert-cancel" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center"><p><?= Yii::t('admin', 'orders.modal_cancel_message') ?></p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'orders.modal_cancel_cancel') ?></button>
                        <a href="#" class="btn btn-primary submit_action"><?= Yii::t('admin', 'orders.modal_cancel_submit') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>