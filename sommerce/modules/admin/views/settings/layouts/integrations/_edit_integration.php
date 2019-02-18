<?php

use common\models\stores\StoreIntegrations;
use sommerce\modules\admin\widgets\IntegrationSettingsForm;
use yii\helpers\Html;
use my\helpers\Url;

/* @var $integration StoreIntegrations */

?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('/settings/layouts/_left_menu', [
                'active' => 'integrations'
            ])?>
        </div>
            <div class="m-grid__item m-grid__item--fluid m-wrapper">
                <!-- BEGIN: Subheader -->
                <div class="m-subheader ">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="m-subheader__title">
                                <?= $integration['name'] ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <!-- END: Subheader -->
                <div class="m-content">
                        <?php if (isset($integration['settings_description']) && $integration['settings_description'] !== ''): ?>
                            <div class="settings-integrations__card mb-4 text-center">
                                <?= $integration['settings_description'] ?>
                            </div>
                        <?php endif; ?>
                        <form id="editIntegrationForm" action="<?= Url::toRoute(['/settings/edit-integration', 'id' => $integration['id']]) ?>" method="post" role="form">
                            <?= Html::beginForm(); ?>
                            <?= IntegrationSettingsForm::widget([
                                    'settingsForm' => $integration['settings_form'],
                                    'options' => $integration['options'],
                            ]) ?>

                            <div class="text-sm-right">
                                <a href="<?= Url::toRoute(['/settings/integrations']) ?>" class="btn btn-secondary mr-3">
                                    <?= Yii::t('admin', 'settings.integrations_edit.cancel_button') ?>
                                </a>
                                <button type="submit" class="btn btn-success m-btn--air"><?= Yii::t('admin', 'settings.integrations_edit.save_button') ?></button>
                            </div>
                            <?= Html::endForm(); ?>
                        </form>
                </div>
            </div>
    </div>
</div>