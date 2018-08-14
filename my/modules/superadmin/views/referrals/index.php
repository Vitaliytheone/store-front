<?php
/* @var $this yii\web\View */
/* @var $referrals \my\modules\superadmin\models\search\PanelsSearch */
/* @var $filters */

use my\helpers\Url;
use my\helpers\SpecialCharsHelper;
use my\components\ActiveForm;

    $this->context->addModule('superadminReferralsController');
?>
<ul class="nav nav-pills mb-3">
    <li class="mr-auto">

    </li>
    <li class="ml-auto">
        <?php $form = ActiveForm::begin([
                'id' => 'referralsSearch',
                'method' => 'get',
                'action' => Url::toRoute(array_merge(['/referrals'], $filters, ['query' => null])),
                'options' => [
                    'class' => "form",
                ],
        ]) ?>
            <div class="input-group">
                <input type="text" class="form-control" name="query" placeholder="<?= Yii::t('app/superadmin', 'referrals.list.search') ?>" value="<?= SpecialCharsHelper::multiPurifier($filters['query']) ?>">
                <span class="input-group-append">
                    <button class="btn btn-light" type="submit"><span class="fa fa-search"></span></button>
                </span>
            </div>
        <?php ActiveForm::end(); ?>
    </li>
</ul>
<?= $this->render('layouts/_referrals_list', [
        'referrals' => $referrals,
        'filters' => $filters
])?>
