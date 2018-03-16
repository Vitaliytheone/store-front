<?php
    /* @var $this yii\web\View */
    /* @var $emails \my\modules\superadmin\models\search\NotificationEmailSearch */
    /* @var $email \common\models\panels\NotificationEmail */

    use my\helpers\Url;
    use yii\bootstrap\Html;
    use common\models\panels\NotificationEmail;
?>
<table class="table mb-0">
    <thead>
    <tr>
        <th class="border-0">Subject</th>
        <th class="border-0">Status</th>
        <th class="border-0"></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($emails) : ?>
        <?php foreach ($emails as $email) : ?>
            <tr <?= (NotificationEmail::STATUS_DISABLED == $email->enabled ? 'class="text-muted"' : '') ?>>
                <td><?= $email->subject ?></td>
                <td><?= $email->getStatusName() ?></td>
                <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <?= Html::a('Edit email' , Url::toRoute(['/settings/edit-email', 'id' => $email->id]), [
                                'class' => 'dropdown-item',
                            ])?>

                            <?php if ($email->enabled) : ?>
                                <?= Html::a('Disable' , Url::toRoute(['/settings/email-status', 'id' => $email->id, 'status' => NotificationEmail::STATUS_DISABLED]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php else : ?>
                                <?= Html::a('Enable' , Url::toRoute(['/settings/email-status', 'id' => $email->id, 'status' => NotificationEmail::STATUS_ENABLED]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php endif; ?>
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