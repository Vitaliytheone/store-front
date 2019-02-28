<?php

    use yii\helpers\Html;
    use store\modules\admin\components\Url;

    /** @var $form \store\modules\admin\models\forms\EditLanguageForm */

    $messagesBySection = $form->getMessagesBySection();
?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'languages'
            ])?>
            <!-- END: Aside Menu -->
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            <?= Yii::t('admin', 'settings.languages_edit_language') ?> <?= $form->getLanguage()->getName() ?>
                        </h3>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">

                <ul class="nav nav-tabs  m-tabs-line m-tabs-line--primary" role="tablist">
                    <?php foreach ($messagesBySection as $sectionKey => $section): ?>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link <?php if($section === reset($messagesBySection)): ?>active show<?php endif; ?>" data-toggle="tab" href="#<?= $sectionKey ?>" role="tab" aria-selected="false"><?= $section['name'] ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <form id="pageForm" class="form-horizontal" action="<?= Url::toRoute(['/settings/edit-language', 'code' => $form->code]) ?>" method="post" role="form">
                    <?= Html::beginForm() ?>

                        <div class="tab-content">
                            <?php foreach ($form->getMessagesBySection() as $sectionKey => $section): ?>
                                <div class="tab-pane <?php if($section === reset($messagesBySection)): ?>active<?php endif; ?>" id="<?= $sectionKey ?>" role="tabpanel">
                                    <?php if(isset($section['messages']) && is_array($section['messages'])): ?>
                                        <?php foreach ($section['messages'] as $messageKey => $message): ?>
                                            <div class="form-group m-form__group">
                                                <label><?= $messageKey ?></label>
                                                <input type="text" class="form-control m-input" name="EditLanguageForm[messages][<?= $messageKey ?>]" placeholder="<?= $message['default'] ?>" value="<?= $message['message'] ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <button class="btn btn-success m-btn--air" type="submit"><?= Yii::t('admin', 'settings.languages_edit_save') ?></button>
                    <a href="<?= Url::toRoute('/settings/languages') ?>" class="btn btn-secondary m-btn--air ml-2"><?= Yii::t('admin', 'settings.languages_edit_cancel') ?></a>

                    <?= Html::endForm() ?>
                </form>

            </div>
        </div>
    </div>
</div>

