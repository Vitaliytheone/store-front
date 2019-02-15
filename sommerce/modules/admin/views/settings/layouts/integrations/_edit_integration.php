<?php

use common\models\stores\StoreIntegrations;
use sommerce\modules\admin\widgets\IntegrationSettingsForm;
use yii\helpers\Html;
use my\helpers\Url;

/* @var $integration StoreIntegrations */

?>

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
            <div class="settings-integrations__card mb-4 text-center">
                <?= $integration['settings_description'] ?>
            </div>
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