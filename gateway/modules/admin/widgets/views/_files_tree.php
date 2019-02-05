<?php

/* @var $this \yii\web\View */
/* @var $items array */

use yii\bootstrap\Html;
use admin\components\Url;
?>

<div class="sommerce-editorPage__list-wrap">

    <?php foreach ($items as $type => $folder) : ?>
        <div class="sommerce-editorPage__list-row">

            <div class="sommerce-editorPage__list-header d-flex" data-toggle="collapse" href="#folder_<?= $type ?>">
                <div class="sommerce-editorPage__list-collapse-item"></div>
                <div class="sommerce-editorPage__list-file d-flex align-items-center">
                    <div class="sommerce-editorPage__list-file-icon"></div>
                    <div class="sommerce-editorPage__list-name"><?= $folder['name'] ?></div>
                </div>
            </div>

            <div class="sommerce-editorPage__list-block collapse show" id="folder_<?= $type ?>">
                <?php if ($folder['can']['add_file']) : ?>
                    <div class="sommerce-editorPage__list-add">
                        <?= Html::a(Yii::t('admin', 'settings.files_add_file'), Url::toRoute(['/settings/create-file',]), [
                            'class' => 'create-file',
                            'data' => [
                                'type' => $type,
                                'extension' => $folder['extension']
                            ]
                        ]) ?>
                    </div>
                <?php endif; ?>

                <?php if ($folder['can']['upload_file']) : ?>
                    <div class="sommerce-editorPage__list-add">
                        <?= Html::a(Yii::t('admin', 'settings.files_add_image'), Url::toRoute(['/settings/upload-file',]), [
                            'class' => 'upload-file'
                        ]) ?>
                    </div>
                <?php endif; ?>

                <ul>
                    <?php foreach ($folder['files'] as $file) : ?>
                        <li>
                            <?php if ($file['can']['update'] || $file['can']['preview']) : ?>
                                <?= Html::a(Html::tag('span', '', [
                                    'class' => 'fa fa-file' . (!$file['active'] ? '-o' : ''),
                                ]) . Html::tag('span', $file['name'], [
                                    'class' => 'filename'
                                ]), Url::toRoute(['/settings/files', 'id' => $file['id'],]), [
                                    'data' => [
                                        'toggle' => 'm-tooltip',
                                        'placement' => 'left',
                                        'original-title' => $file['modified']
                                    ]
                                ])?>
                            <?php else : ?>
                                <?= Html::tag('span', Html::tag('span', '', [
                                    'class' => 'fa fa-file' . (!$file['active'] ? '-o' : ''),
                                ]) . Html::tag('span', $file['name'], [
                                    'class' => 'filename'
                                ]))?>
                            <?php endif; ?>

                            <?php if (!empty($file['can']['rename']) || !empty($file['can']['delete']) || !empty($file['can']['download'])) : ?>
                                <div class="sommerce-editorPage__list-block-actions">
                                    <div class="dropdown">
                                        <button class="action-btn" data-toggle="dropdown">
                                            <span class="fa fa-ellipsis-h"></span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <?php if (!empty($file['can']['rename'])) : ?>
                                                <?= Html::a(Yii::t('admin', 'settings.files_rename_file'), Url::toRoute(['/settings/rename-file', 'id' => $file['id'],]), [
                                                    'class' => 'dropdown-item rename-file',
                                                    'data' => [
                                                        'details' => [
                                                            'name' => $file['name'],
                                                        ]
                                                    ]
                                                ])?>
                                            <?php endif; ?>
                                            <?php if (!empty($file['can']['download'])) : ?>
                                                <?= Html::a(Yii::t('admin', 'settings.files_download_file'), Url::toRoute(['/settings/download-file', 'id' => $file['id'],]), [
                                                    'class' => 'dropdown-item download-file',
                                                ])?>
                                            <?php endif; ?>
                                            <?php if (!empty($file['can']['delete'])) : ?>
                                                <?= Html::a(Yii::t('admin', 'settings.files_delete_file'), Url::toRoute(['/settings/delete-file', 'id' => $file['id'],]), [
                                                    'class' => 'dropdown-item delete-file',
                                                    'data' => [
                                                        'title' => Yii::t('admin', 'settings.files_confirm_delete_file')
                                                    ]
                                                ])?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>

        </div>
    <?php endforeach; ?>
</div>
