<?php
    /* @var $this yii\web\View */
    /* @var $domains[] \common\models\sommerces\Domains */

    use common\models\sommerces\Domains;
    use common\models\sommerces\Orders;
    use yii\bootstrap\Html;

    $domainColors = [
        Domains::STATUS_EXPIRED => 'text-danger',
        Domains::STATUS_OK => 'text-success',
    ];

    $orderColors = [
        Orders::STATUS_PENDING => '',
        Orders::STATUS_PAID => '',
        Orders::STATUS_ERROR => '',
        Orders::STATUS_CANCELED => 'text-muted',
    ];

    $colors = function($domain) use ($domainColors, $orderColors) {
        if ('order' == $domain['type']) {
            return $orderColors[$domain['status']];
        } else {
            return $domainColors[$domain['status']];
        }
    }
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"><?= Yii::t('app', 'domains.list.header')?></h2>
    </div>
</div>
<?php if (!empty($domains)): ?>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'domains.list.domain_column')?></th>
                        <th><?= Yii::t('app', 'domains.list.created_column')?></th>
                        <th><?= Yii::t('app', 'domains.list.expiry_column')?></th>
                        <th><?= Yii::t('app', 'domains.list.status_column')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($domains as $domain): ?>
                    <tr>
                        <td><?= $domain['domain'] ?></td>
                        <td>
                            <?= $domain['date']; ?>
                        </td>
                        <td>
                            <?= $domain['expired']; ?>
                        </td>
                        <td class="<?= $colors($domain) ?>">
                            <?= $domain['statusName'] ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
