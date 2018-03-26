<?php
    /* @var $this yii\web\View */
    /* @var $staffs \my\modules\superadmin\models\search\StaffSearch */
    /* @var $staff \common\models\panels\SuperAdmin */

    use my\helpers\Url;
    use yii\bootstrap\Html;
    use common\models\panels\SuperAdmin;

    $rulesList = array_keys(SuperAdmin::getDefaultRules());
?>

<table class="table mb-0">
    <thead>
    <tr>
        <th class="border-0">Account</th>
        <th class="border-0">Status</th>
        <th class="border-0">Access</th>
        <th class="border-0 text-nowrap">Last auth</th>
        <th class="border-0"></th>
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
                <tr <?= (SuperAdmin::STATUS_SUSPENDED == $staff->status ? 'class="text-muted"' : '') ?>>
                    <td><?= $staff->username ?></td>
                    <td><?= $staff->getStatusName() ?></td>
                    <td><?= (([] == array_diff($rulesList, $details['access'])) ? 'Full access' : 'Limited access') ?></td>
                    <td>
                        <span class="text-nowrap">
                            <?= $staff->getFormattedDate('last_login', 'php:Y-m-d') ?>
                        </span>
                        <?= $staff->getFormattedDate('last_login', 'php:H:i:s') ?>
                    </td>
                    <td class="text-right">
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <?= Html::a('Edit account' , Url::toRoute(['/settings/edit-staff', 'id' => $staff->id]), [
                                    'class' => 'dropdown-item edit-account',
                                    'data-details' => json_encode($details)
                                ])?>
                                <?= Html::a('Set password' , Url::toRoute(['/settings/staff-password', 'id' => $staff->id]), [
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
                <td colspan="5">No staffs</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>