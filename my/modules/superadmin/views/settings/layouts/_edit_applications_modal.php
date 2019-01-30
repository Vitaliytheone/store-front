<?php
/* @var $this yii\web\View */

use yii\bootstrap\Html;

?>
<div class="modal fade" id="editApplicationsModal" tabindex="-1" data-backdrop="static" role="dialog"
     aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="ModalLabel"><?= Yii::t('app/superadmin', 'applications.edit.modal_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>

            <div class="modal-footer">
                <button type="button" class="btn  btn-light"
                        data-dismiss="modal"><?= Yii::t('app/superadmin', 'applications.edit.modal_cancel_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'applications.edit.modal_submit_btn'), [
                    'class' => 'btn btn-primary',
                    'name' => 'edit-plan-button',
                    'id' => 'editApplicationsButton'
                ]) ?>
            </div>
        </div>
    </div>
</div>