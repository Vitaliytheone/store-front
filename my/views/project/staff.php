<?php
    /* @var $this yii\web\View */
    /* @var $staffs \common\models\panels\ProjectAdmin */
    /* @var $staff \common\models\panels\ProjectAdmin */
    /* @var $panel \common\models\panels\Project */

    use yii\bootstrap\Html;
    use yii\helpers\Json;
    use common\models\panels\ProjectAdmin;
?>
<div class="row">
  <div class="col-lg-12">
    <h2 class="page-header"><?= Yii::t('app', 'panels.staff.header', [
            'site' => $panel->getSite()
        ])?></small> <button class="btn btn-outline btn-success" id="createStaff"><?= Yii::t('app', 'panels.staff.create_new_account')?></button></h2>
  </div>
</div>
<div class="row">
  <div class="col-lg-8">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th><?= Yii::t('app', 'panels.staff.column_account')?></th>
          <th><?= Yii::t('app', 'panels.staff.column_status')?></th>
          <th><?= Yii::t('app', 'panels.staff.column_access')?></th>
          <th nowrap><?= Yii::t('app', 'panels.staff.column_last_login')?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($staffs as $staff): ?>

          <tr<?php if ($staff->status == ProjectAdmin::STATUS_SUSPENDED) echo ' class="text-muted"' ?>>
            <td><?= $staff->login ?></td>
            <td><?= $staff->getStatusName() ?></td>
            <td><?= ($staff->isFullAccess() ? Yii::t('app', 'panels.staff.full_access') : Yii::t('app', 'panels.staff.limited_access')) ?></td>
            <td>
                <?= $staff->getFormattedDate('last_login'); ?>
            </td>
            <td>
              <div class="btn-group">
                <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-caret-down fa-fw"></i></button>
                <ul class="dropdown-menu pull-right">
                  <li>
                      <?= Html::a(Yii::t('app', 'panels.staff.action_edit_account'), ["/staff/edit/" . $staff->id], [
                          'class' => 'edit-staff',
                          'data-details' => json_encode([
                              'id' => $staff->id,
                              'login' => $staff->login,
                              'status' => $staff->status,
                              'accessList' => $staff->getRules()
                          ])
                      ])?>
                  </li>
                  <li>
                      <?= Html::a(Yii::t('app', 'panels.staff.action_set_password'), ["/staff/passwd/" . $staff->id], [
                          'class' => 'set-staff-password',
                          'data-details' => Json::encode($staff->getAttributes(['id', 'login']))
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

<?= $this->render('layouts/_create_staff_modal', ['panel' => $panel]); ?>
<?= $this->render('layouts/_edit_staff_modal'); ?>
<?= $this->render('layouts/_edit_staff_password_modal'); ?>
