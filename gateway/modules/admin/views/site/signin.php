<?php

    use yii\helpers\Html;
    use common\components\ActiveForm;

    /** @var $form \gateway\modules\admin\models\forms\LoginForm */

?>
<div class="m-grid m-grid--hor m-grid--root m-page">

    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--singin m-login--2 m-login-2--skin-2"
         id="m_login">
        <div class="m-grid__item m-grid__item--fluid	m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__signin">
                    <div class="m-login__head">
                        <h3 class="m-login__title"><?= Yii::t('admin', 'login.sign_in_header') ?></h3>
                    </div>
                    <form class="m-login__form m-form" action="" method="post">
                        <?= Html::beginForm() ?>
                        <?php if($form->hasErrors()): ?>
                            <div class="error-summary alert alert-danger"><?= ActiveForm::firstError($form) ?></div>
                        <?php endif; ?>

                        <div class="form-group m-form__group">
                            <input class="form-control m-input" type="text" placeholder="<?= Yii::t('admin', 'login.sign_in_username_placeholder') ?>" name="username" value="<?= $form->username ?>" autocomplete="off">
                        </div>
                        <div class="form-group m-form__group">
                            <input class="form-control m-input m-login__form-input--last" type="password" placeholder="<?= Yii::t('admin', 'login.sign_in_password_placeholder') ?>" name="password">
                        </div>
                        <div class="m-login__form-action">
                            <button type="submit" id="m_login_signin_submit" class="btn btn-primary m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">
                                <?= Yii::t('admin', 'login.sign_in_submit_title') ?>
                            </button>
                        </div>
                        <?= Html::endForm() ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>