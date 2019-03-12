<?php

use store\modules\admin\components\Url;

/** @var $id */
/** @var $name */

$updateUrl = Url::toRoute(['/settings/update-nav', 'id'=> $id]);
$getNavUrl = Url::toRoute(['/settings/get-nav', 'id'=> $id]);
$deleteUrl = Url::toRoute(['/settings/delete-nav', 'id'=> $id]);
$editBtnText = Yii::t('admin', 'settings.nav_bt_edit');
$deleteBtnText = Yii::t('admin', 'settings.nav_bt_delete');

?>
<div class="dd-handle"><?= $name ?></div>
<div class="dd-edit-button">
    <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm" data-submit_url="<?= $updateUrl ?>" data-get_url="<?= $getNavUrl ?>" data-toggle="modal" data-target=".edit_navigation">
        <?= $editBtnText ?>
    </a>
    <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-delete_url="<?= $deleteUrl ?>" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="<?= $deleteBtnText ?>">
        <i class="la la-trash"></i>
    </a>
</div>
