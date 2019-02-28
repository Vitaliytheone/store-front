<div class="modal fade" id="modal-duplicate" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <?= Yii::t('admin', 'pages.duplicate_confirm') ?>
                        <a class="btn btn-secondary cursor-pointer m-btn--air mr-3" data-dismiss="modal"><?= Yii::t('admin', 'pages.cancel') ?></a>
                        <a class="btn btn-primary" id="feature-delete"><?= Yii::t('admin', 'pages.duplicate') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

