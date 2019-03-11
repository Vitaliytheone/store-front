<?php

use sommerce\modules\admin\components\Url;
use yii\helpers\Html;

/* @var $host string */
/* @var $pages array */

?>
<!-- Page Content Start -->
<div class="page-container">

    <div class="m-container-sommerce container-fluid">
        <div class="row">
            <div class="col">
                <div class="sommerce-block">
                    <a href="#" id="btn-new-page" class="btn btn-primary m-btn m-btn--icon" data-toggle="modal" data-target="#modal-create-page"
                       data-action="<?= Url::toRoute(['/pages/create-page']) ?>"
                       data-modal-title="<?= Yii::t('admin', 'pages.add') ?>">
                         <?= Yii::t('admin', 'pages.create_page')?>
                    </a>
                </div>
            </div>
        </div>
        <div class="sommerce-page mt-3">

            <div class="divTable sommerce-page__table-header">
                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableCell sommerce-page__td-name sommerce-page__table-header-td">
                            <?= Yii::t('admin', 'pages.name')?>
                        </div>
                        <div class="divTableCell sommerce-page__td-data">
                            <div class="divTableCell sommerce-page__td-status sommerce-page__table-header-td">
                                <?= Yii::t('admin', 'pages.status')?>
                            </div>
                            <div class="divTableCell sommerce-page__td-date sommerce-page__table-header-td">
                                <?= Yii::t('admin', 'pages.last_updated')?>
                            </div>
                            <div class="divTableCell sommerce-page__td-action sommerce-page__table-header-td ">
                                <span class="sommerce-page__table-header-actions">
                                    <span class="la la-ellipsis-h"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php  foreach ($pages as $page): ?>
                <div class="divTable sommerce-page__row <?= !$page['visibility'] ? 'sommerce-page__row-disabled' : '' ?>">
                    <div class="divTableBody">
                        <div class="divTableRow sommerce-page__row-tr">
                            <div class="divTableCell sommerce-page__row-body-td sommerce-page__td-name">
                                <?= htmlspecialchars($page['name']) ?>
                                <?php if ($page['visibility']): ?>
                                    <a href="<?= $host . '/' . $page['url']?>" class="sommerce-page__td-link" target="_blank">
                                        <span class="la la-external-link"></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="divTableCell sommerce-page__row-body-td sommerce-page__td-data">

                                <div class="sommerce-page__td-data-actions">
                                    <!--<a href="#" class="sommerce-page__actions-link duplicate-page"
                                       data-page="<?= htmlspecialchars(json_encode($page)) ?>"
                                       data-action="<?=Url::toRoute(['/pages/duplicate-page', 'id' => $page['id']]) ?>">
                                        <span class="la la-clone"></span>
                                        <?= Yii::t('admin', 'pages.duplicate') ?>
                                    </a>-->
                                    <a href="#" class="sommerce-page__actions-link edit-page"
                                       data-page="<?= htmlspecialchars(json_encode($page)) ?>"
                                       data-action="<?= Url::toRoute(['/pages/update-page', 'id' => $page['id']]) ?>"
                                       data-modal-title="<?= Yii::t('admin', 'pages.update') ?>">
                                        <span class="la la-cog"></span>
                                        <?= Yii::t('admin', 'pages.settings')?>
                                    </a>
                                    <a href="<?= Url::toRoute('/pages/edit-page/' . $page['id'])?>" class="sommerce-page__actions-btn m-btn--air ">
                                        <span class="la la-edit"></span>
                                        <?= Yii::t('admin', 'pages.editor')?>
                                    </a>
                                </div>

                                <div class="divTableCell sommerce-page__td-status">
                                    <span class="text-warning"><?= $page['status'] ?></span>
                                </div>
                                <div class="divTableCell sommerce-page__td-date">
                                    <span><?= $page['updated_at_formatted'] ?></span>
                                </div>
                                <div class="divTableCell sommerce-page__td-action text-right">
                                    <span class="la la-ellipsis-h"></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?= $this->render('_modal_add_page', ['host' => $host]); ?>
<?= $this->render('_modal_duplicate_page'); ?>
