<?php
    /* @var $this yii\web\View */

    use yii\bootstrap\Html;
?>
<div class="modal fade" id="editPaymentModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Edit payment method</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <?= Html::submitButton('Save changes', [
                    'class' => 'btn btn-primary',
                    'name' => 'edit-payment-button',
                    'id' => 'editPaymentButton'
                ]) ?>
            </div>
        </div>
    </div>
</div>