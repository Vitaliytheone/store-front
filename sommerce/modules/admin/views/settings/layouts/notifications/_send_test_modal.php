<?php
    /* @var $this \yii\web\View */
?>
<div class="modal fade notification-test-send" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send test</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group m-form__group">
                    <label for="select-admin">Send to</label>
                    <select class="form-control m-input m-input--square" id="select-admin">
                        <option>adminmail@mail.ru</option>
                        <option>admin2@yandex.ru</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>