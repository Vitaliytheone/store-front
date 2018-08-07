<?php
/* @var $this yii\web\View */
/* @var $sslList \my\modules\superadmin\models\search\SslSearch */
/* @var $navs \my\modules\superadmin\models\search\SslSearch */
/* @var $status */

use my\helpers\Url;
use my\helpers\SpecialCharsHelper;

$this->context->addModule('superadminSslController');
?>
    <div class="container-fluid mt-3">
        <ul class="nav mb-3">
            <li class="mr-auto">
                <ul class="nav nav-pills">
                    <?php foreach ($navs as $code => $label) : ?>
                        <?php $code = is_numeric($code) ? $code : null;?>
                        <li class="nav-item"><a class="nav-link text-nowrap <?= ($code === $status ? 'active' : '') ?>" href="<?= Url::toRoute(['/ssl', 'status' => $code]) ?>"><?= $label ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li>
                <form class="form-inline" method="GET" id="sslSearch" action="<?=Url::toRoute(array_merge(['/ssl'], $filters, ['query' => null]))?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'ssl.list.search')?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                        <span class="input-group-btn">
                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                    </span>
                    </div>
                </form>
            </li>
        </ul>

        <?= $this->render('layouts/_ssl_list', [
            'sslList' => $sslList
        ])?>
    </div>
<?= $this->render('layouts/_ssl_details_modal')?>