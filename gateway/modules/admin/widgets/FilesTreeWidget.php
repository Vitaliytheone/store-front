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

        $menuTree = '';
        $currentFileId = ArrayHelper::getValue($this->file, 'id');
        foreach ($this->files as $type => $files) {
            $folderName = Yii::t('admin', 'settings.files_type.' . $type);
            $filesItems = '';

            foreach ($files as $file) {
                $modified = '';

                $selected = $currentFileId === $file['id'];
                $modifiedAt = ArrayHelper::getValue($file, 'updated_at');

                if ($modifiedAt) {
                    $modified = Html::tag('span',
                        Yii::t('admin', 'settings.themes_modified') . ' ' . $modifiedAt,
                        ['class' => 'jstree-tooltip']
                    );
                }

                if (Files::can(Files::CAN_UPDATE, $file)) {
                    $fileItem = Html::tag('a', $file['name'] . $modified, [
                        'class' => $selected ? 'jstree-clicked' : '',
                        'href' => Url::toRoute([
                            '/settings/files',
                            'id' => $file['id'],
                        ]),
                    ]);
                } else {
                    $fileItem = Html::tag('span', $file['name'] . $modified, [
                        'class' => $selected ? 'jstree-clicked' : '',
                    ]);
                }

                $fileItem = Html::tag('li', $fileItem, [
                    'id' => $file['name'],
                    'data-jstree' => $modifiedAt ?
                        '{ "type" : "file", "icon":"fa fa-file" }' :
                        '{ "type" : "file", "icon":"fa fa-file-o" }'
                ]);

                $filesItems .= $fileItem;
            }

            $filesItems = Html::tag('ul', $filesItems, ['class' => 'my-ul']);

            $menuTree .= Html::tag('li', $folderName . $filesItems, [
                'data-jstree' => '{ "opened" : true }',
            ]);
        }

        return $menuTree;
    }
}