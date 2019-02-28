<?php
/* @var $this yii\web\View */
/* @var $params \superadmin\models\search\ApplicationsSearch */

/* @var $param \common\models\sommerces\Params */

use control_panel\helpers\Url;
use yii\bootstrap\Html;

?>
<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th scope="col"><?= Yii::t('app/superadmin', 'settings.applications.column_name') ?></th>
        <th class="table-custom__action-th"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($params)) : ?>
        <?php foreach ($params as $param) : ?>
            <tr>
                <td><?= $param['code'] ?></td>
                <td class="text-right">
                    <?= Html::a(Yii::t('app/superadmin', 'settings.applications.edit_label'),
                        Url::toRoute(['/settings/edit-application', 'code' => $param['code']]),
                        ['class' => 'btn btn-primary btn-sm edit-applications',]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="8"><?= Yii::t('app/superadmin', 'settings.applications.no_content') ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>