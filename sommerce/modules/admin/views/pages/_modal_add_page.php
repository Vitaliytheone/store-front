<?php

use common\components\ActiveForm;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\EditPageForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $host string */

$model = new EditPageForm();
?>


<div class="modal fade" id="modal-create-page" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-middle" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?= Yii::t('admin', 'pages.new')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'pageForm',
                'enableClientScript' => false,
                'action' => Url::toRoute('/pages/create-page'),
                'method' => 'post',
            ]); ?>
            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'createPageError'
                ]); ?>

                <?= $form->field($model, 'name') ?>


                <div class="card card-white mb-4">
                    <div class="card-body">

                        <div class="row seo-header align-items-center">
                            <div class="col-sm-8">
                                <?= Yii::t('admin', 'pages.search_preview')?>
                            </div>
                            <div class="col-sm-4 text-sm-right">
                                <a class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">
                                    <?= Yii::t('admin', 'pages.edit_seo')?>
                                </a>
                            </div>
                        </div>

                        <div class="seo-preview">
                            <div class="seo-preview__title edit-seo__title">
                            </div>
                            <div class="seo-preview__url">
                                <?= $host . '/' ?><span class="edit-seo__url"></span>
                            </div>
                            <div class="seo-preview__description edit-seo__meta">

                            </div>
                        </div>

                        <div class="collapse" id="seo-block">
                            <?= $form->field($model, 'title',
                                [
                                    'template' => "{label}\n{input}\n<small class='form-text text-muted'><span class='edit-seo__title-muted'></span>" .
                                        Yii::t('admin', 'pages.chars', ['count' => '70']) .
                                        "</small>",
                                    'options' => [
                                        'class' => 'form-group'
                                    ]

                                ]
                            )->textInput(['id' => 'edit-seo__title']) ?>

                            <?= $form->field($model, 'description',
                                [
                                    'template' => "{label}\n{input}\n<small class='form-text text-muted'><span class='edit-seo__meta-muted'></span>" .
                                        Yii::t('admin', 'pages.chars', ['count' => '160']) .
                                        "</small>",
                                    'options' => [
                                        'class' => 'form-group'
                                    ]

                                ]
                                )->textarea([
                                    'id' => 'edit-seo__meta',
                                    'rows' => 3
                                ])
                            ?>

                            <?= $form->field($model, 'keywords'
                                )->textarea([
                                    'id' => 'edit-seo__meta-keyword',
                                    'rows' => 3
                                ])
                            ?>

                            <?= $form->field($model, 'url',
                                [
                                    'template' => "{label}\n<div class='input-group'><span class='input-group-addon' id='basic-addon3'>" .
                                        $host . '/'. "</span>{input}\n</div>",
                                    'options' => [
                                        'class' => 'form-group'
                                    ]

                                ]
                                )->textInput([
                                    'id' => 'edit-seo__url',
                                ])
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group">
                    <div class="m-switch-group">
                        <span class="m-switch m-switch--sm">
                            <label>
                                <input type="checkbox" value="1" checked="checked" name="EditPageForm[visibility]" id="check-visibility">
                                   <span></span>
                            </label>
                        </span>
                        <label class="m-switch-label"><?= Yii::t('admin', 'pages.visibility')?></label>
                    </div>
                </div>

            </div>
            <div class="modal-footer text-right d-flex justify-content-between">
                <div>
                    <div class="btn btn-modal-delete" style="display:none;">
                        <div class="sommerce-dropdown__delete">
                            <div class="sommerce-dropdown__delete-description">
                               <?= Yii::t('admin', 'pages.modal.are_you_sure') ?>
                            </div>
                            <a href="#" class="btn btn-danger btn-sm mr-2 sommerce-dropdown__delete-cancel"><?= Yii::t('admin', 'pages.cancel')?></a>

                            <?= Html::a(Yii::t('admin', 'pages.delete'),
                                [Url::toRoute(array_merge(['/pages/delete-page']))], [
                                    'class' => 'delete-page btn btn-secondary btn-sm'
                                ])
                            ?>

                        </div>
                        <?= Yii::t('admin', 'pages.delete')?>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary mr-3" data-dismiss="modal"><?= Yii::t('admin', 'pages.cancel')?></button>
                    <button type="submit" id="page-submit" class="btn btn-primary"><?= Yii::t('admin', 'pages.add')?></button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>