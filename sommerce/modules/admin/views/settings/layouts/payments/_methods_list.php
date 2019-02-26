<?php

use sommerce\modules\admin\components\Url;
use sommerce\helpers\UiHelper;
use common\models\stores\StorePaymentMethods;

/* @var $paymentMethods[]|\common\models\stores\StorePaymentMethods */
/* @var $method \common\models\stores\StorePaymentMethods */
/* @var $availableMethods array */

$icons = StorePaymentMethods::getMethodIcon();
$names = StorePaymentMethods::getNames();
?>

    <div class="m-subheader ">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title">
                    <?= Yii::t('admin', 'settings.payments_title') ?>
                </h3>
            </div>
            <div>
                <?php if (!empty($availableMethods)) : ?>
                    <div class="m-dropdown--align-right">
                        <a href="<?= Url::toRoute(['/settings/add-payment-method']) ?>" onclick="return false"
                           class="btn btn-primary m-btn--air btn-brand cursor-pointer add-method"><?= Yii::t('admin', 'settings.payments_add') ?></a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="m-content">
        <div class="dd">
            <ol class="dd-list">

                <?php foreach ($paymentMethods as $method): ?>

                    <li class="dd-item" data-id="<?= $method->id ?>">
                        <div class="sommerce-settings__payment-cart m-portlet">
                            <div class="row align-items-center payment-main-block">
                                <div class="col-12">
                                    <div class="payment-cart__preview">
                                        <img src="<?= $icons[$method->method_id]['icon'] ?>" alt="" class="img-fluid" style="">
                                    </div>
                                    <div id="met-<?= $method->method_id ?>" class="payment-cart__title <?= $method->visibility ? '' : 'text-muted' ?>">
                                        <?= $names[$method->method_id] ?: $method->name ?>
                                    </div>
                                    <div class="payment-cart__control d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="payment-cart__active">
                                                <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                                                    <label>
                                                        <input class="toggle-active" type="checkbox"
                                                               name="toggle-active" <?= UiHelper::toggleString($method->visibility, 'checked') ?>
                                                               data-payment_method="<?= $method->method_id ?>"
                                                               data-action_url="<?= Url::toRoute(['/settings/payments-toggle-active', 'method' => $method->id]) ?>">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                            <div class="payment-cart__actions">
                                                <a href="<?= Url::toRoute(['/settings/payments-settings', 'method' => $method->id]) ?>"
                                                   class="btn m-btn--pill m-btn--air btn-primary">
                                                    <?= Yii::t('admin', 'settings.payments_edit_method') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                <?php endforeach; ?>
            </ol>
        </div>
    </div>

<?= $this->render('_modal_add_method', ['availableMethods' => $availableMethods]) ?>