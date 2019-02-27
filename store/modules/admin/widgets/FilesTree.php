<?php

namespace store\modules\admin\widgets;

use store\modules\admin\components\Url;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;

class FilesTree extends Widget
{
    /** @var  string Current real Theme folder name */
    public $themeFolder;

    /**
     * Current edited file
     * @var $currentFile string
     */
    public $currentFile;

    /**
     * Theme folders/files structure
     * @var array
     */
    public $filesTree = [];

    /** @inheritdoc */
    public function init()
    {
        parent::init();

    }

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
        if (empty($this->filesTree) || !is_array($this->filesTree)) {
            return null;
        }

        $menuTree = '';

        foreach ($this->filesTree as $folderName => $files) {

            $filesItems = '';

            foreach ($files as $file) {
                $fileItem = '';
                $modified = '';

                $selected = $this->currentFile === ArrayHelper::getValue($file, 'name');
                $modifiedAt = ArrayHelper::getValue($file, 'modified_at');

                if ($modifiedAt) {
                    $modified = Html::tag('span',
                        Yii::t('admin', 'settings.themes_modified') . ' ' . $modifiedAt,
                        ['class' => 'jstree-tooltip']
                    );
                }

                $fileItem = Html::tag('a', $file['name'] . $modified, [
                    'class' => $selected ? 'jstree-clicked' : '',
                    'href' => Url::toRoute([
                        '/settings/edit-theme',
                        'theme' => $this->themeFolder,
                        'file' => $file['name'],
                    ]),
                ]);

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