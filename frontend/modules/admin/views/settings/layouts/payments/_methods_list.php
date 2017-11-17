<?php

use frontend\modules\admin\components\Url;

?>

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
                            <a href="<?= Url::toRoute('/settings/payments') ?>"
                               class="btn m-btn--pill m-btn--air btn-primary">
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
                            <a href="<?= Url::toRoute('/settings/payments') ?>"
                               class="btn m-btn--pill m-btn--air btn-primary">
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
                            Bitcoiit
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
                            <a href="<?= Url::toRoute('/settings/payments') ?>"
                               class="btn m-btn--pill m-btn--air btn-primary">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
