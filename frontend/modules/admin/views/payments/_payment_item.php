<?php

use frontend\modules\admin\components\Url;

/* @var $this yii\web\View */
/* @var $payment array */

?>

<tr>
    <td><?= $payment['id'] ?></td>
    <td><?= $payment['customer'] ?></td>
    <td><?= $payment['amount'] ?></td>
    <td><?= $payment['method_title'] ?></td>
    <td ><?= $payment['fee'] ?></td>
    <td><?= $payment['memo'] ?></td>
    <td><?= $payment['status_title'] ?></td>
    <td nowrap=""><?= $payment['updated_at_formatted'] ?></td>
    <td class="text-right">
        <div class="m-dropdown m-dropdown--small m-dropdown--inline m-dropdown--arrow m-dropdown--align-right" data-dropdown-toggle="click" aria-expanded="true">
            <a href="#" class="m-dropdown__toggle btn btn-primary btn-sm">
                <?= Yii::t('admin', 'payments.action_title') ?>
                <span class="fa fa-cog"></span>
            </a>
            <div class="m-dropdown__wrapper">
                <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                <div class="m-dropdown__inner">
                    <div class="m-dropdown__body">
                        <div class="m-dropdown__content">
                            <ul class="m-nav">
                                <li class="m-nav__item">
                                    <a href="#" class="m-nav__link" data-toggle="modal" data-target=".payments_detail" data-backdrop="static" data-id="<?= $payment['id'] ?>" data-modal_title="<?= Yii::t('admin', 'payments.details_title', ['payment_id' => $payment['id']] ) ?>" data-action_url="<?= Url::toRoute(['/payments/get-details', 'id' => $payment['id']]) ?>">
                                        <span class="m-nav__link-text">
                                            <?= Yii::t('admin', 'payments.action_details') ?>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>