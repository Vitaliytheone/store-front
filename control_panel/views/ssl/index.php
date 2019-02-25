<?php
    /* @var $this yii\web\View */
    /* @var $sslList[] \control_panel\models\search\SslSearch */
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"><?= Yii::t('app', 'ssl.list.header')?> <a href="/ssl/order" class="btn btn-outline btn-success"><?= Yii::t('app', 'ssl.list.order_ssl')?></a></h2>
    </div>
</div>
<?php if (!empty($sslList)): ?>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th><?= Yii::t('app', 'ssl.list.domain_column')?></th>
                    <th><?= Yii::t('app', 'ssl.list.product_column')?></th>
                    <th><?= Yii::t('app', 'ssl.list.created_column')?></th>
                    <th><?= Yii::t('app', 'ssl.list.expiry_column')?></th>
                    <th><?= Yii::t('app', 'ssl.list.status_column')?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($sslList as $ssl): ?>
                    <tr>
                        <td><?= $ssl['domain'] ?></td>
                        <td><?= $ssl['sslItem'] ?></td>
                        <td>
                            <?= $ssl['date']; ?>
                        </td>
                        <td>
                            <?= (!empty($ssl['expired']) ? $ssl['expired'] : ''); ?>
                        </td>
                        <td>
                            <?= $ssl['statusName']; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
