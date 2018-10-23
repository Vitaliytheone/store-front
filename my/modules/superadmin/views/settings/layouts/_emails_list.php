<?php
    /* @var $this yii\web\View */
    /* @var $emails \my\modules\superadmin\models\search\NotificationEmailSearch */
    /* @var $email \common\models\panels\NotificationEmail */

    use my\helpers\Url;
    use yii\bootstrap\Html;
    use common\models\panels\NotificationEmail;
    use yii\helpers\Json;
?>
<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th scope="col"><?= Yii::t('app/superadmin', 'email.list.subject') ?></th>
        <th scope="col"><?= Yii::t('app/superadmin', 'email.list.status') ?></th>
        <th class="table-custom__action-th"></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($emails) : ?>
        <?php foreach ($emails as $email) : ?>
            <tr <?= (NotificationEmail::STATUS_DISABLED == $email->enabled ? 'class="disabled-row"' : '') ?>>
                <td><?= $email->subject ?></td>
                <td><?= $email->getStatusName() ?></td>
                <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'email.dropdown.actions') ?></button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <?= Html::a(Yii::t('app/superadmin', 'email.dropdown.edit_email'), Url::toRoute(['/settings/edit-email', 'id' => $email->id]), [
                                'class' => 'dropdown-item edit-email',
                                'data-details' => Json::encode($email),
                            ])?>

                            <?php if ($email->enabled) : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'email.dropdown.disable'), Url::toRoute(['/settings/email-status', 'id' => $email->id, 'status' => NotificationEmail::STATUS_DISABLED]), [
                                    'class' => 'dropdown-item',
                                ])?>
                            <?php else : ?>
                                <?= Html::a(Yii::t('app/superadmin', 'email.dropdown.enable'), Url::toRoute(['/settings/email-status', 'id' => $email->id, 'status' => NotificationEmail::STATUS_ENABLED]), [
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
            <td colspan="5"><?= Yii::t('app/superadmin', 'email.list.no_staffs') ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>