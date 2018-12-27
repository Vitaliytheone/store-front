<?php

/* @var $this \yii\web\View */
/* @var $model \admin\models\forms\EditPaymentMethodForm */

?>
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'payments'
            ])?>
        </div>
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <?= $this->render('layouts/payments/_edit_payment_method', [
                    'model' => $model,
                ]);
            ?>
        </div>
    </div>
</div>