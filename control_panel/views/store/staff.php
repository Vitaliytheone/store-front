<?php
    /* @var $this yii\web\View */
    /* @var $canCreate */
    /* @var $staffs StoreAdmins[] */
    /* @var $staff StoreAdmins */
    /* @var $store \common\models\stores\Stores */

    use yii\bootstrap\Html;
    use yii\helpers\Json;
    use common\models\stores\StoreAdmins;
?>
<div class="row">
  <div class="col-lg-12">
    <h2 class="page-header"><?= Yii::t('app', 'panels.staff.header', [
            'site' => $store->getBaseDomain()
        ])?> <?php if ($canCreate): ?><button class="btn btn-outline btn-success" id="createStaff"><?= Yii::t('app', 'stores.staff.create_new_account')?></button><?php endif; ?>
    </h2>
  </div>
</div>
<div class="row">
  <div class="col-lg-8">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th><?= Yii::t('app', 'stores.staff.column_account')?></th>
          <th><?= Yii::t('app', 'stores.staff.column_status')?></th>
          <th><?= Yii::t('app', 'stores.staff.column_access')?></th>
          <th nowrap><?= Yii::t('app', 'stores.staff.column_last_login')?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($staffs as $staff): ?>

          <tr<?php if ($staff->status == StoreAdmins::STATUS_SUSPENDED) echo ' class="text-muted"' ?>>
            <td><?= $staff->username ?></td>
            <td><?= $staff->getStatusName() ?></td>
            <td><?= ($staff->isFullAccess() ? Yii::t('app', 'stores.staff.full_access') : Yii::t('app', 'stores.staff.limited_access')) ?></td>
            <td>
                <?= $staff->getFormattedDate('last_login'); ?>
            </td>
            <td>
              <div class="btn-group">
                <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-caret-down fa-fw"></i></button>
                <ul class="dropdown-menu pull-right">
                  <li>
                      <?= Html::a(Yii::t('app', 'stores.staff.action_edit_account'), ["/stores/staff/edit/" . $staff->id], [
                          'class' => 'edit-staff',
                          'data-details' => json_encode([
                              'id' => $staff->id,
                              'login' => $staff->username,
                              'status' => $staff->status,
                              'accessList' => $staff->getRules()
                          ])
                      ])?>
                  </li>
                  <li>
                      <?= Html::a(Yii::t('app', 'stores.staff.action_set_password'), ["/stores/staff/password/" . $staff->id], [
                          'class' => 'set-staff-password',
                          'data-details' => Json::encode([
                              'id' => $staff->id,
                              'login' => $staff->username
                          ])
                      ])?>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->render('layouts/_create_staff_modal', ['store' => $store]); ?>
<?= $this->render('layouts/_edit_staff_modal'); ?>
<?= $this->render('layouts/_edit_staff_password_modal'); ?>
