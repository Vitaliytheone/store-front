<?php

/* @var $this \yii\web\View  */
/* @var $integrations array */

use my\helpers\SpecialCharsHelper;
use common\models\stores\Integrations;
use my\helpers\Url;

?>
<div class="m-grid__item m-grid__item--fluid m-wrapper">
    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title">
                    <?= Yii::t('admin', 'settings.integrations_page_title'); ?>
                </h3>
            </div>
        </div>
    </div>
    <!-- END: Subheader -->
    <div class="m-content">
        <div class="settings-integrations__block">
            <div class="settings-integrations__block-title"><?= Yii::t('admin', 'settings.integrations_chats_title') ?></div>
            <?php foreach (SpecialCharsHelper::multiPurifier($integrations['chats']) as $chatKey => $chat): ?>
            <div class="sommerce-settings__payment-cart m-portlet">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="payment-cart__preview">
                            <img src="<?= Integrations::getIconByCode($chat['code']) ?>" alt="<?= $chat['name'] ?>" class="img-fluid">
                        </div>
                        <div class="payment-cart__title">
                            <?= $chat['name'] ?>
                        </div>
                        <div class="payment-cart__control d-flex justify-content-between align-items-center">
                            <div>
                                <div class="payment-cart__active">
                                    <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                                        <label>
                                            <input class="toggle-active" type="checkbox"
                                               name="toggle-active" <?= (bool)$chat['visibility'] ? 'checked' : '' ?>
                                               data-action_url="<?= Url::toRoute(['/settings/integrations-toggle-active', 'id' => $chat['id']]) ?>">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                                <div class="payment-cart__actions">
                                    <a href="<?= Url::toRoute(['/settings/edit-integration', 'id' => $chat['id']]) ?>" class="btn m-btn--pill m-btn--air btn-primary">
                                        <?= Yii::t('admin', 'settings.integrations_edit_title') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="settings-integrations__block">
            <div class="settings-integrations__block-title"><?=Yii::t('admin', 'settings.integrations_analytics_title') ?></div>
            <?php foreach (SpecialCharsHelper::multiPurifier($integrations['analytics']) as $key => $analytics): ?>
            <div class="sommerce-settings__payment-cart m-portlet">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="payment-cart__preview">
                            <img src="<?= Integrations::getIconByCode($analytics['code']) ?>" alt="<?= $analytics['name'] ?>" class="img-fluid">
                        </div>
                        <div class="payment-cart__title">
                            <?= $analytics['name'] ?>
                        </div>
                        <div class="payment-cart__control d-flex justify-content-between align-items-center">
                            <div>
                                <div class="payment-cart__active">
                                    <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                                        <label>
                                            <input class="toggle-active" type="checkbox"
                                                   name="toggle-active" <?= (bool)$analytics['visibility'] ? 'checked' : '' ?>
                                                   data-action_url="<?= Url::toRoute(['/settings/integrations-toggle-active', 'id' => $analytics['id']]) ?>">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                                <div class="payment-cart__actions">
                                    <a href="<?= Url::toRoute(['/settings/edit-integration', 'id' => $analytics['id']]) ?>" class="btn m-btn--pill m-btn--air btn-primary">
                                        <?= Yii::t('admin', 'settings.integrations_edit_title') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
