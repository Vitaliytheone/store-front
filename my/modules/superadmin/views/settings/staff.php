<?php
    /* @var $this yii\web\View */
    /* @var $staffs \my\modules\superadmin\models\search\StaffSearch */

    use my\helpers\Url;
    use yii\bootstrap\Html;

    $this->context->addModule('superadminStaffsController');
?>
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-2 offset-lg-1">
            <ul class="nav nav-pills flex-column mb-3">
                <li class="nav-item">
                    <?= Html::a('Payments', Url::toRoute('/settings'), ['class' => 'nav-link'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a('Staff', Url::toRoute('/settings/staff'), ['class' => 'nav-link bg-faded'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a('Email', Url::toRoute('/settings/email'), ['class' => 'nav-link'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a('Plan', Url::toRoute('/settings/plan'), ['class' => 'nav-link'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a(Yii::t('app/superadmin', 'pages.settings.content'), Url::toRoute('/settings/content'), ['class' => 'nav-link'])?>
                </li>
            </ul>
        </div>
        <div class="col-lg-8">
            <button type="button" class="btn btn-secondary mb-3" id="createStaff">Add account</button>
            <div class="card">
                <div class="card-block">
                    <?= $this->render('layouts/_staffs_list', [
                        'staffs' => $staffs
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render('layouts/_change_staff_password_modal'); ?>
<?= $this->render('layouts/_create_staff_modal'); ?>
<?= $this->render('layouts/_edit_staff_modal'); ?>
