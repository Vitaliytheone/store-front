<?php
/* @var $this yii\web\View */
/* @var array $domains */

?>
<table class="table">
    <thead>
    <tr>
        <th></th>
        <th><?= Yii::t('app', 'panels.order.search_result_domain_column') ?></th>
        <th><?= Yii::t('app', 'panels.order.search_result_price_column') ?></th>
        <th><?= Yii::t('app', 'panels.order.search_result_status_column') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($domains as $domain) : ?>
        <tr>
            <?php if ($domain['is_available']) : ?>
                <td><input type="radio" name="zone" class="domain_zone" value="<?= $domain['zone'] ?>" data-domain="<?= $domain['domain'] ?>"></td>
                <td><?= $domain['domain'] ?></td>
                <td><?= $domain['price'] ?></td>
                <td><?= Yii::t('app', 'panels.order.search_result_free_value') ?></td>
            <?php else : ?>
                <td>X</td>
                <td><?= $domain['domain'] ?></td>
                <td><?= $domain['price'] ?></td>
                <td><?= Yii::t('app', 'panels.order.search_result_taken_value') ?></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
