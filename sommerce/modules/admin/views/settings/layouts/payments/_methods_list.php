<?php

use sommerce\modules\admin\components\Url;
use sommerce\helpers\UiHelper;

/* @var $paymentMethods[]|\common\models\stores\StorePaymentMethods */
/* @var $method \common\models\stores\StorePaymentMethods */
/* @var $availableMethods array */

?>

    <div class="m-subheader ">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title">
                    <?= Yii::t('admin', 'settings.payments_title') ?>
                </h3>
            </div>
            <div>
                <div class="m-dropdown--align-right">
                    <a href="<?= Url::toRoute(['/settings/add-payment-method']) ?>"
                       class="btn btn-primary m-btn--air btn-brand cursor-pointer add-method"><?= Yii::t('admin', 'settings.payments_add') ?></a>
                </div>
            </div>
        </div>
    </div>

    <div class="m-content">
        <div class="dd" id="nestable">
            <ol class="dd-list">

                <?php foreach ($paymentMethods as $method): ?>

                    <li class="dd-item" data-id="<?= $method->id ?>">
                        <div class="dd-handle-pay table__drag dd-icon-pay ui-sortable-handle">
<!--                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title>-->
<!--                                <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path>-->
<!--                            </svg>-->
                        </div>
                        <div class="sommerce-settings__payment-cart m-portlet <?= $method->visibility ? '' : 'text-muted' ?>">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="payment-cart__preview">
                                        <img src="<?= $method->getMethodIcon() ?>" alt="" class="img-fluid" style="">
                                    </div>
                                    <div class="payment-cart__title">
                                        <?= $method->name ?: $method->getName() ?>
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