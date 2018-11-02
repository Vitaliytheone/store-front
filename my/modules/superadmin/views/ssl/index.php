<?php
/* @var $this yii\web\View */
/* @var $sslList \superadmin\models\search\SslSearch */
/* @var $navs \superadmin\models\search\SslSearch */
/* @var $status */

use my\helpers\Url;
use my\helpers\SpecialCharsHelper;

$this->context->addModule('superadminSslController');
?>
        <ul class="nav nav-pills mb-3" role="tablist">
            <?php foreach ($navs as $code => $label) : ?>
                <?php $code = is_numeric($code) ? $code : null;?>
                <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/ssl', 'status' => $code]) ?>"><?= $label ?></a></li>
            <?php endforeach; ?>
            <li class="ml-auto">
                <form class="form" method="GET" id="sslSearch" action="<?=Url::toRoute(array_merge(['/ssl'], $filters, ['query' => null]))?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'ssl.list.search')?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                        <div class="input-group-append">
                            <button class="btn btn-light" type="submit"><span class="fa fa-search"></span></button>
                        </div>
                    </div>
                </form>
            </li>
        </ul>

        <?= $this->render('layouts/_ssl_list', [
            'sslList' => $sslList
        ])?>
<?= $this->render('layouts/_ssl_details_modal')?>