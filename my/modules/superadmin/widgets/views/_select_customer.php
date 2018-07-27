<?php

use my\helpers\Url;

/* @var $this yii\web\View */
/* @var $models array  */
/* @var $status string|int */
/* @var $name string */
/* @var $selectedCustomerId int */
/* @var $context */


$context->addModule('superadminSelectCustomerController');
?>
<select data-action="<?= Url::toRoute(['/customers/ajax-customers', 'status' => $status]) ?>" id="editstoreform-customer_id" class="selectpicker w-100 customers-select" name="<?= $name ?>" data-live-search="true">
    <?php foreach ($models as $customer) : ?>
        <option id="editstoreform-customer_id_option" data-tokens="<?= $customer->email ?>" value="<?= $customer->id ?>"
            <?= (!empty($selectedCustomerId) && $customer->id == $selectedCustomerId ? 'selected' : '') ?>>
            <?= $customer->email ?>
        </option>
    <?php endforeach; ?>
</select>
