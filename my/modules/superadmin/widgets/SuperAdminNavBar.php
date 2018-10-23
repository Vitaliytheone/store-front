<?php

namespace superadmin\widgets;

use ReflectionClass;
use yii\bootstrap\NavBar;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\bootstrap\BootstrapPluginAsset;

/**
 * SuperAdminNavBar renders a navbar HTML component in super admin module.
 *
 * Any content enclosed between the [[begin()]] and [[end()]] calls of NavBar
 * is treated as the content of the navbar. You may use widgets such as [[Nav]]
 * or [[\yii\widgets\Menu]] to build up such content. For example,
 *
 * ```php
 * use yii\bootstrap\NavBar;
 * use yii\bootstrap\Nav;
 *
 * NavBar::begin(['brandLabel' => 'NavBar Test']);
 * echo Nav::widget([
 *     'items' => [
 *         ['label' => 'Home', 'url' => ['/site/index']],
 *         ['label' => 'About', 'url' => ['/site/about']],
 *     ],
 *     'options' => ['class' => 'navbar-nav'],
 * ]);
 * NavBar::end();
 * ```
 *
 */
class SuperAdminNavBar extends NavBar
{
    public $toggleOptions = null;    
    /**
    * Initializes the widget.
    */
    public function init()
    {
        $grandparent = $this->_getGrandparentClass();
        $grandparent::init();
        $this->clientOptions = false;
        if (empty($this->options['class'])) {
            Html::addCssClass($this->options, ['navbar', 'navbar-default']);
        } else {
            Html::addCssClass($this->options, ['widget' => 'navbar']);
        }
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'nav');
        echo Html::beginTag($tag, $options);

        echo Html::beginTag('button', $this->toggleOptions);
        echo"<span class=\"navbar-toggler-icon\"></span>";
        echo Html::endTag('button');

        Html::addCssClass($this->containerOptions, ['collapse' => 'collapse', 'widget' => 'navbar-collapse']);
        $options = $this->containerOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        echo Html::beginTag($tag, $options);
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $tag = ArrayHelper::remove($this->containerOptions, 'tag', 'div');
        echo Html::endTag($tag);
        /*if ($this->renderInnerContainer) {
            echo Html::endTag('div');
        }*/
        $tag = ArrayHelper::remove($this->options, 'tag', 'nav');
        echo Html::endTag($tag);
        BootstrapPluginAsset::register($this->getView());
    }

    /**
     *
     * @return string
     */
    private function _getGrandparentClass() {
        if (is_object($this)) {
            $thing = get_class($this);
        }
        $class = new ReflectionClass($this);
        return $class->getParentClass()->getParentClass()->getName();
    }

}