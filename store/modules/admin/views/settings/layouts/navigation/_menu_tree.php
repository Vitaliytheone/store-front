<?php

/**
 * Render Navigation menu item
 * @param $treeItem array
 * @return string
 */
function renderMenuItem($treeItem) {
    $itemId = $treeItem['id'];
    $menuItem = Yii::$app->view->render('_menu_tree_item', [
        'id' => $itemId,
        'name' => $treeItem['name'],
    ]);

    if (isset($treeItem['nodes'])) {
        $menuItem .= '<ol class="dd-list">' . renderMenuTree($treeItem['nodes']) . '</ol>';
    }

    return '<li class="dd-item" data-id="'. $itemId . '">' . $menuItem . '</li>';
}

/**
 * Render navigation menu tree
 * @param $tree array
 * @return string
 */
function renderMenuTree($tree){
    $menuTree = '';
    foreach ($tree as $item) {
        $menuTree .= renderMenuItem($item);
    }

    return $menuTree;
}

/** @var $navTree */
?>

<div class="dd" id="nestable">
    <ol class="dd-list">
        <?= renderMenuTree($navTree) ?>
    </ol>
</div>
