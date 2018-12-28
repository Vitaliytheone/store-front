<?php

use yii\helpers\Html;
use common\components\ActiveForm;
use admin\components\Url;

/** @var \yii\base\Model $form */

?>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            Account
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-content">

                <div class="row">
                    <div class="col-md-4">
                        <?php if($form->hasErrors()): ?>
                            <div class="error-summary alert alert-danger"><?= ActiveForm::firstError($form) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <form id="settings-general-form" action="<?= Url::toRoute('/account') ?>" method="post" name="AccountSettings" role="form">
                    <?= Html::beginForm(); ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group m-form__group">
                                <label for="password">
                                    <?= Yii::t('admin', 'account.current_password') ?>
                                </label>
                                <input type="password" class="form-control m-input" name="AccountForm[current_password]" id="password">
                            </div>
                            <div class="form-group m-form__group">
                                <label for="newPassword">
                                    <?= Yii::t('admin', 'account.new_password') ?>
                                </label>
                                <input type="password" class="form-control m-input"  name="AccountForm[password]" id="newPassword">
                            </div>
                            <div class="form-group m-form__group">
                                <label for="confirmPassword">
                                    <?= Yii::t('admin', 'account.confirm_password') ?>
                                </label>
                                <input type="password" class="form-control m-input" name="AccountForm[confirm_password]" id="confirmPassword">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success m-btn--air" name="save-button">Save changes</button>
                    </div>
                    <?= Html::endForm(); ?>
                </form>

            </div>
        </div>
    </div>
</div>
