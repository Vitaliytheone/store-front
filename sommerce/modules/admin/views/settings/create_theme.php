<?php
use yii\helpers\Html;
use common\components\ActiveForm;

/* @var $this \yii\web\View */

/* @var $theme \common\models\store\CustomThemes */

use sommerce\modules\admin\components\Url;

?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">

        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'themes'
            ]) ?>
        </div>
        <!-- END: Left Aside -->

        <div class="m-grid__item m-grid__item--fluid m-wrapper">

            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.themes_create_title') ?>
                        </h3>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->


            <form id="themeForm" class="form-horizontal" action="<?= Url::toRoute(['/settings/create-theme'])?>" name="ThemeForm" method="post" role="form">
                <?= Html::beginForm() ?>
                    <div class="m-content">

                        <?php if($theme->hasErrors()): ?>
                            <div class="error-summary alert alert-danger"><?= ActiveForm::firstError($theme) ?></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="theme_name">
                                <?= Yii::t('admin', 'settings.themes_theme_name') ?>
                            </label>
                            <input type="text" class="form-control" id="theme_name" name="ThemeForm[name]">
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-success"><?= Yii::t('admin', 'settings.themes_create_save') ?></button>
                    <a href="<?= Url::toRoute(['/settings/themes']) ?>" class="btn btn-secondary"><?= Yii::t('admin', 'settings.themes_create_cancel') ?></a>
                <?= Html::endForm() ?>
            </form>

        </div>

    </div>
</div>
