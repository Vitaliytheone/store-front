<?php

namespace admin\widgets;

use admin\components\Url;
use common\models\gateway\Files;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;

/**
 * Class FilesTreeWidget
 * @package admin\widgets
 */
class FilesTreeWidget extends Widget
{
    /**
     * Current edited file
     * @var Files
     */
    public $file;

    /**
     * Theme folders/files structure
     * @var array
     */
    public $files = [];

    /**
     * @return string
     */
    public function run()
    {
        $menuTree = $this->_filesTree();

        $menuTree = Html::tag('ul', $menuTree, []);
        $menuTree = Html::tag('div', $menuTree, [
            'id' => 'm_tree_1',
            'class' => 'tree-demo'
        ]);

        return $menuTree;
    }

    /**
     * Render two-level folders-files tree menu
     * @return null|string
     */
    private function _filesTree()
    {
        if (empty($this->files) || !is_array($this->files)) {
            return null;
        }

        $currentFileId = ArrayHelper::getValue($this->file, 'id');
        $itemsTree = [];

        foreach ($this->files as $type => $files) {
            $folderName = Yii::t('admin', 'settings.files_type.' . $type);

            $items = [];

            foreach ($files as $file) {
                $items[$file['id']] = ArrayHelper::merge($file, [
                    'active' => $currentFileId == $file['id'],
                    'modified' => !empty($file['updated_at']) ? Yii::t('admin', 'settings.files_modified') . ' ' . Files::formatDate($file['updated_at']) : null,
                    'can' => [
                        'update' => Files::can(Files::CAN_UPDATE, $file),
                        'rename' => Files::can(Files::CAN_RENAME, $file),
                        'delete' => Files::can(Files::CAN_DELETE, $file),
                    ]
                ]);
            }

            $itemsTree[$type] = [
                'name' => $folderName,
                'can' => [
                    'add_file' => !in_array($type, [Files::FILE_TYPE_IMAGE]),
                    'upload_file' => in_array($type, [Files::FILE_TYPE_IMAGE]),
                ],
                'files' => $items,
            ];
        }

        return $this->render('_files_tree', [
            'items' => $itemsTree
        ]);
    }
}