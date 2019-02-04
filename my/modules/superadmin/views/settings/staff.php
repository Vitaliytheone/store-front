<?php
    /* @var $this yii\web\View */
    /* @var $staffs \superadmin\models\search\StaffSearch */

    use my\helpers\Url;
    use yii\bootstrap\Html;

    $this->context->addModule('superadminStaffsController');
?>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group list-group__custom">
                <?= $this->render('layouts/_menu'); ?>
            </div>
        </div>
        <div class="col-md-9">
            <div class="mb-3">
                <button type="button" class="btn btn-light" id="createStaff"><?= Yii::t('app/superadmin', 'settings.staff.add_account') ?></button>
            </div>
            <?= $this->render('layouts/_staffs_list', [
                    'staffs' => $staffs
            ]) ?>
        </div>
    </div>
</div>

<?= $this->render('layouts/_change_staff_password_modal'); ?>
<?= $this->render('layouts/_create_staff_modal'); ?>
<?= $this->render('layouts/_edit_staff_modal'); ?>
