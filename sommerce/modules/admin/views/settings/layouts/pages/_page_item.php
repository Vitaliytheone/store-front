<?php

use sommerce\modules\admin\components\Url;
use yii\helpers\Html;

/* @var $page array */
/* @var $this \yii\web\View */

?>

<tr class="<?= $page['visibility'] ? '' : 'text-muted' ?>">
    <td><?= $page['id'] ?></td>
    <td class="sommerce-table__no-wrap"><?= Html::encode($page['title']) ?></td>
    <td class="sommerce-table__no-wrap"><?= $page['updated_at_formatted'] ?></td>
    <td><?= $page['visibility_title'] ?></td>
    <td class="sommerce-table__no-wrap text-lg-right">
        <a class="btn m-btn--pill m-btn--air btn-sm btn-primary" href="<?= Url::toRoute(['/settings/update-page', 'id' => $page['id']]) ?>">
            <?= Yii::t('admin', 'settings.pages_edit') ?>
        </a>
        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="modal" data-target="#delete-modal" data-action_url="<?= Url::toRoute(['/settings/delete-page', 'id' => $page['id']]) ?>" title="<?= Yii::t('admin', 'settings.pages_delete') ?>">
            <i class="la la-trash"></i>
        </a>
    </td>
</tr>

