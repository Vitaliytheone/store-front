<?php
    /* @var $this \yii\web\View */

    use frontend\modules\admin\components\Url;
?>
<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'payments'
            ])?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="m-subheader__title">
                            Payments
                        </h3>
                    </div>
                </div>
            </div>
            <!-- END: Subheader -->
            <div class="m-content">
                <div class="sommerce-settings__payment-cart m-portlet">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <div class="payment-cart__preview">
                                <img src="/img/paypal.png" alt="" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="payment-cart__title">
                                        PayPal
                                    </div>
                                </div>
                                <div>
                                    <div class="payment-cart__active">
                               <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
																		<label>
																			<input type="checkbox" checked name="">
																			<span></span>
																		</label>
																	</span>
                                    </div>
                                    <div class="payment-cart__actions">
                                        <a href="<?= Url::toRoute('/settings/payments')?>" class="btn m-btn--pill m-btn--air btn-primary">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sommerce-settings__payment-cart m-portlet">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <div class="payment-cart__preview">
                                <img src="/img/2checkout.png" alt="" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="payment-cart__title">
                                        2Checkout
                                    </div>
                                </div>
                                <div>
                                    <div class="payment-cart__active">
                               <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
																		<label>
																			<input type="checkbox" name="">
																			<span></span>
																		</label>
																	</span>
                                    </div>
                                    <div class="payment-cart__actions">
                                        <a href="<?= Url::toRoute('/settings/payments')?>" class="btn m-btn--pill m-btn--air btn-primary">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sommerce-settings__payment-cart m-portlet">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <div class="payment-cart__preview">
                                <img src="/img/bitcoin.png" alt="" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="payment-cart__title">
                                        Bitcoiit                                        </div>
                                </div>
                                <div>
                                    <div class="payment-cart__active">
                               <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
																		<label>
																			<input type="checkbox" name="">
																			<span></span>
																		</label>
																	</span>
                                    </div>
                                    <div class="payment-cart__actions">
                                        <a href="<?= Url::toRoute('/settings/payments')?>" class="btn m-btn--pill m-btn--air btn-primary">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end::Body -->