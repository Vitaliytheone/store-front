<?php
    /* @var $this yii\web\View */
    /* @var $referrals \my\modules\superadmin\models\search\PanelsSearch */
    /* @var $filters array */

    use my\helpers\Url;
    use yii\bootstrap\Html;
    use my\helpers\SpecialCharsHelper;
    use yii\widgets\LinkPager;
    use my\modules\superadmin\widgets\CountPagination;
?>
<table class="table table-sm table-custom">
    <thead>
        <tr>
            <th><?= $referrals['sort']->link('customers.id', ['class' => 'sort_link']) ?></th>
            <th><?= $referrals['sort']->link('customers.email', ['class' => 'sort_link'])?></th>
            <th><?= $referrals['sort']->link('total_visits', ['class' => 'sort_link'])?></th>
            <th><?= $referrals['sort']->link('unpaid_referrals', ['class' => 'sort_link'])?></th>
            <th><?= $referrals['sort']->link('paid_referrals', ['class' => 'sort_link'])?></th>
            <th><?= Yii::t('app/superadmin', 'referrals.list.conversion_rate')?></th>
            <th><?= $referrals['sort']->link('total_earnings', ['class' => 'sort_link'])?></th>
            <th><?= $referrals['sort']->link('unpaid_earnings', ['class' => 'sort_link'])?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($referrals['models'])) : ?>
            <?php foreach (SpecialCharsHelper::multiPurifier($referrals['models']) as $referral) : ?>
                <tr>
                    <td>
                        <?= $referral['id'] ?>
                    </td>
                    <td>
                        <?= $referral['email'] ?>
                    </td>
                    <td>
                        <?php if ($referral['total_visits']) : ?>
                            <?= Html::a($referral['total_visits'], Url::toRoute(['/referrals/total-visits', 'id' => $referral['id']])) ?>
                        <?php else : ?>
                            <?= $referral['total_visits'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($referral['unpaid_referrals']) : ?>
                            <?= Html::a($referral['unpaid_referrals'], Url::toRoute(['/referrals/unpaid-referrals', 'id' => $referral['id']])) ?>
                        <?php else : ?>
                            <?= $referral['unpaid_referrals'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($referral['paid_referrals']) : ?>
                            <?= Html::a($referral['paid_referrals'], Url::toRoute(['/referrals/paid-referrals', 'id' => $referral['id']])) ?>
                        <?php else : ?>
                            <?= $referral['paid_referrals'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $referral['conversion_rate'] ?>
                    </td>
                    <td>
                        <?php if ($referral['total_earnings']) : ?>
                            <?= Html::a($referral['total_earnings'], Url::toRoute(['/referrals/total-earnings', 'id' => $referral['id']])) ?>
                        <?php else : ?>
                            <?= $referral['total_earnings'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $referral['unpaid_earnings'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div class="row">
    <div class="col-md-6">
        <!-- Pagination Start -->
        <nav>
            <ul class="pagination">
                <?= LinkPager::widget([
                    'pagination' => $referrals['pages'],
                ]); ?>
            </ul>
        </nav>
        <!-- Pagination End -->
    </div>
    <div class="col-md-6 text-md-right">
        <?= CountPagination::widget([
            'pages' => $referrals['pages'],
            'params' => $filters,
        ]) ?>
    </div>
</div>