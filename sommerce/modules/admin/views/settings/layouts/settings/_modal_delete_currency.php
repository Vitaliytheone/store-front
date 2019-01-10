<?php

/* @var $this yii\web\View */

?>

<div class="modal fade" id="delete-modal-pay" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-trash-o"></span>
                        <p><?= Yii::t('admin', 'settings.general_delete_payments_agree') ?></p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'settings.general_delete_cancel') ?></button>
                        <a href="#" class="btn btn-danger m-btn--air" id="payments-delete"><?= Yii::t('admin', 'settings.general_delete_submit') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>