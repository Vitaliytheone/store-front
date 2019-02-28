<?php
    /* @var $this yii\web\View */
?>
<div class="modal fade" id="sslSubmitDisableModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="mb-0"><?= Yii::t('app/superadmin', 'ssl.list.submit_disable_modal_header')?></h5>
            </div>
            <div class="modal-footer modal-footer__padding-10 justify-content-center">
                <button type="button" class="btn btn-lg btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'ssl.list.submit_disable_no') ?></button>
                <button type="button" class="btn btn-lg btn-primary disable_ssl_submit" data-href=""><?= Yii::t('app/superadmin', 'ssl.list.submit_disable_yes') ?></button>
            </div>
        </div>
    </div>
</div>