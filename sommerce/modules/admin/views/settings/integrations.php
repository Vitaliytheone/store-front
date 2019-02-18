<?php

use common\models\stores\StoreIntegrations;

/* @var $this \yii\web\View */
/* @var $integrations array */
/* @var $integration StoreIntegrations */
/* @var $editPage bool|null */

?>
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'integrations'
            ])?>
        </div>
            <?= $this->render('layouts/integrations/_integrations_list', [
                'integrations' => $integrations,
            ]); ?>
    </div>
</div>