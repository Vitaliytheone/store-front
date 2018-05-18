<?php
    /* @var $this \yii\web\View */
?>
<div class="modal fade notification-preview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-lg-preview" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <iframe src="http://front.sommerce.net/sommerce_notifications/order_confirmation.html" frameborder="0" class="notification-preview__iframe"></iframe>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>