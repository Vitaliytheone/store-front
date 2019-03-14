<?php

/* @var $this yii\web\View */
/* @var $rates array */

?>
<div class="container">

    <div class="row">
        <div class="col-sm-4 col-sm-offset-4">

            <table class="table">
                <thead>
                <tr>
                    <th><?= Yii::t('app/superadmin', 'tools.exchange_rates.field.currency') ?></th>
                    <th><?= Yii::t('app/superadmin', 'tools.exchange_rates.field.exchange_rate') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rates as $rate): ?>
                    <tr>
                        <td><?= $rate['currency'] ?></td>
                        <td><?= $rate['exchange_rate'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>
