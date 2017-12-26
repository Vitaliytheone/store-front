<?php

namespace frontend\modules\admin\widgets;

use frontend\modules\admin\components\Url;
use yii\base\Widget;
use yii\helpers\Html;

class FilesTree extends Widget
{
    /** @var $filesTree array */
    public $filesTree = [];

    /** @var string  Theme full path */
    public $themePath;

    /** @var  string Theme folder name */
    public $themeFolder;

    /**
     * Current edited file
     * @var $currentFile string
     */
    public $currentFile;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        if (!$this->themeFolder) {
            $this->themeFolder = array_slice(explode('/', $this->themePath), -1)[0];
        }
    }

    /**
     * @return string
     */
    public function run()
    {
        $menuTree = $this->filesTree($this->filesTree);

        $menuTree = Html::tag('ul', $menuTree, []);
        $menuTree = Html::tag('div', $menuTree, [
            'id' => 'm_tree_1',
            'class' => 'tree-demo'
        ]);

        return $menuTree;
    }

    /**
     * Return files tree item
     * @param $itemName
     * @param $item
     * @return string
     */
    private function fileTreeItem($itemName, $item)
    {
        $isFolder = isset($item['files']);

        $menuItem = $itemName;

        if ($isFolder) {
            $menuItem .= Html::tag('ul', $this->filesTree($item['files']), ['class' => 'my-ul']);
        } else {

            $relativeFilePath = ltrim(str_replace($this->themePath, '', $item['path_name']), '/');
            $selected = $this->currentFile === $relativeFilePath;

            $menuItem = Html::tag('a', $menuItem, [
                'class' => $selected ? 'jstree-clicked' : '',
                'href' => Url::toRoute([
                    '/settings/edit-theme',
                    'folder' => $this->themeFolder,
                    'file' => $relativeFilePath,
                ]),
            ]);
        }

        $menuItem = Html::tag('li', $menuItem, [
            'data-jstree' => $isFolder ? '{ "opened" : true }' :  '{ "type" : "file" }',
            'class' => '',
        ]);

        return $menuItem;
    }

    /**
     * Return files tree menu
     * @param $tree array
     * @return string
     */
    private function filesTree($tree){

        $menuTree = '';

        foreach ($tree as $itemKey => $item) {
            $menuTree .= static::fileTreeItem($itemKey, $item);
        }

        return $menuTree;
    }

}