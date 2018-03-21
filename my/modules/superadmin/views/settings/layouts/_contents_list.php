<?php
/* @var $this yii\web\View */
/* @var $contents \my\modules\superadmin\models\search\ContentSearch */
/* @var $content \common\models\panels\Content */

use my\helpers\Url;
use yii\bootstrap\Html;
?>

<table class="table mb-0">
    <thead>
    <tr>
        <th class="border-0"><?= Yii::t('app/superadmin', 'settings.content.column_name') ?></th>
        <th class="border-0"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($contents)) : ?>
        <?php foreach ($contents as $content) : ?>
            <tr>
                <td><?= $content->name ?></td>
                <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= Yii::t('app/superadmin', 'settings.content.actions_label') ?></button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <?= Html::a(Yii::t('app/superadmin', 'settings.content.action_edit_content') , Url::toRoute(['/settings/edit-content', 'id' => $content->id]), [
                                'class' => 'dropdown-item edit-content',
                                'data-details' => json_encode($content->getAttributes())
                            ])?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="8"><?= Yii::t('app/superadmin', 'settings.content.no_content')?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>