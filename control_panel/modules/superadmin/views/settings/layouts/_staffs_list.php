<?php
    /* @var $this yii\web\View */
    /* @var $staffs \superadmin\models\search\StaffSearch */
    /* @var $staff \common\models\sommerces\SuperAdmin */

    use control_panel\helpers\Url;
    use yii\bootstrap\Html;
    use common\models\sommerces\SuperAdmin;

    $rulesList = array_keys(SuperAdmin::getDefaultRules());
?>

<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th scope="col"><?= Yii::t('app/superadmin', 'staff.list.account') ?></th>
        <th scope="col"><?= Yii::t('app/superadmin', 'staff.list.status') ?></th>
        <th scope="col"><?= Yii::t('app/superadmin', 'staff.list.access') ?></th>
        <th scope="col"><?= Yii::t('app/superadmin', 'staff.list.access') ?></th>
        <th class="table-custom__action-th"></th>
    </tr>
    </thead>
    <tbody>
        <?php if ($staffs) : ?>
            <?php foreach ($staffs as $staff) : ?>
                <?php
                    $details = [
                        'status' => $staff->status,
                        'username' => $staff->username,
                        'first_name' => $staff->first_name,
                        'last_name' => $staff->last_name,
                        'access' => $staff->getAccessRules()
                    ];
                ?>
                <tr <?= (SuperAdmin::STATUS_SUSPENDED == $staff->status ? 'class="disabled-row"' : '') ?>>
                    <td><?= $staff->username ?></td>
                    <td><?= $staff->getStatusName() ?></td>
                    <td><?= (([] == array_diff($rulesList, $details['access'])) ? Yii::t('app/superadmin', 'staff.list.access_full') : Yii::t('app/superadmin', 'staff.list.access_limited')) ?></td>
                    <td>
                        <span class="text-nowrap">
                            <?= $staff->getFormattedDate('last_login', 'php:Y-m-d') ?>
                        </span>
                        <?= $staff->getFormattedDate('last_login', 'php:H:i:s') ?>
                    </td>
                    <td class="text-right">
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'staff.list.dropdown_actions') ?></button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <?= Html::a(Yii::t('app/superadmin', 'staff.list.dropdown_edit_account') , Url::toRoute(['/settings/edit-staff', 'id' => $staff->id]), [
                                    'class' => 'dropdown-item edit-account',
                                    'data-details' => json_encode($details)
                                ])?>
                                <?= Html::a(Yii::t('app/superadmin', 'staff.list.dropdown_set_password') , Url::toRoute(['/settings/staff-password', 'id' => $staff->id]), [
                                    'class' => 'dropdown-item change-password',
                                    'data-details' => json_encode($details)
                                ])?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="5"><?= Yii::t('app/superadmin', 'staff.list.no_staffs') ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>