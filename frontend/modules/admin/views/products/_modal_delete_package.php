<?php

use yii\helpers\Url;

/* @var $this yii\web\View */

?>

<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-success_redirect="<?= Url::to(['/admin/products'])?>">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-trash-o"></span>
                        <p><?= Yii::t('admin', 'products.package_delete_agree') ?></p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'products.package_delete_cancel') ?></button>
                        <button class="btn btn-danger m-btn--air" id="feature-delete"><?= Yii::t('admin', 'products.package_delete_submit') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>