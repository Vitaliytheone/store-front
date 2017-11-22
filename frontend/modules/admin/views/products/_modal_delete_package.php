<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $formatter yii\i18n\Formatter */
$formatter = Yii::$app->formatter;

?>

<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-success_redirect="<?= Url::to(['/admin/products'])?>">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col modal-delete-block text-center">
                        <span class="fa fa-trash-o"></span>
                        <p>Are your sure that your want to delete this Package?</p>
                        <button class="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger m-btn--air" id="feature-delete">Yes, delete it!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>