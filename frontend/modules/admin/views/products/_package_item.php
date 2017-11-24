<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\helpers\UiHelper;

/* @var $this yii\web\View */
/* @var $product array */
/* @var $package array */
/* @var $formatter yii\i18n\Formatter */

$formatter = Yii::$app->formatter;

?>

<!-- Package Item-->
<div class="group-item sommerce_dragtable__tr align-items-center package-item <?= UiHelper::toggleString(!$package['visibility'],'disabled-product')?>" data-action-url="<?= Url::to(['products/move-package', 'id' => $package['id'], 'position' => ""])?>">
    <div class="col-lg-5 padding-null-left">
        <div class="sommerce_dragtable__category-move move">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Drag-Handle</title>
                <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
            </svg>
        </div>
        <strong><?= $package['quantity']?></strong> <?= Html::encode($package['name']) ?>
    </div>
    <div class="col-lg-2">
        <?= $package['price']?>
    </div>
    <div class="col-lg-2">
        <?= UiHelper::switchString($package['mode'], $package['provider'],'Manual') ?>
    </div>
    <div class="col-lg-2 text-lg-center">
        <?= UiHelper::switchString($package['visibility'], 'Enabled','Disabled') ?>
    </div>
    <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
        <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action"
                data-toggle="modal"
                data-target=".add_package"
                data-backdrop="static"
                data-id="<?= $package['id'] ?>"
                data-get-url="<?= Url::to(['products/get-package', 'id' => $package['id']]) ?>"
                data-action-url="<?= Url::to(['products/update-package', 'id' => $package['id']]) ?>">
            <?= \Yii::t('admin', 'products.edit_package') ?>
        </button>
        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
           title="<?= \Yii::t('admin', 'products.delete_package') ?>"
           data-toggle="modal"
           data-target="#delete-modal"
           data-backdrop="static"
           data-action-url="<?= Url::to(['products/delete-package', 'id' => $package['id']]) ?>">
            <i class="la la-trash"></i>
        </a>
    </div>
</div>
<!--/ Package Item-->





