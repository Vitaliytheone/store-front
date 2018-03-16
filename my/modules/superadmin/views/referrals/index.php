<?php
/* @var $this yii\web\View */
/* @var $referrals \my\modules\superadmin\models\search\PanelsSearch */
/* @var $filters */

use my\helpers\Url;
    $this->context->addModule('superadminReferralsController');
?>
<div class="container-fluid mt-3">
    <ul class="nav mb-3">
        <li class="mr-auto">

        </li>
        <li>
            <form class="form-inline" method="GET" id="referralsSearch" action="<?=Url::toRoute(array_merge(['/referrals'], $filters, ['query' => null]))?>">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'referrals.list.search') ?>" value="<?=$filters['query']?>">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="submit"><i class="fa fa-search fa-fw" id="submitSearch"></i></button>
                    </span>
                </div>
            </form>
        </li>
    </ul>
    <?= $this->render('layouts/_referrals_list', [
        'referrals' => $referrals,
    ])?>
</div>
