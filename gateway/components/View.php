<?php
namespace gateway\components;

use Yii;
use yii\base\ViewRenderer;

/**
 * Class View
 * @package gateway\components
 */
class View extends \yii\web\View {

    /**
     * Render content
     * @param string $content
     * @param array $params
     * @param string $ext
     * @return string
     */
    public function renderContent($content, $params = [], $ext = 'twig'): string
    {
        $output = '';
        if (isset($this->renderers[$ext])) {
            if (is_array($this->renderers[$ext]) || is_string($this->renderers[$ext])) {
                $this->renderers[$ext] = Yii::createObject($this->renderers[$ext]);
            }
            /* @var $renderer ViewRenderer */
            $renderer = $this->renderers[$ext];

            if (method_exists($renderer, 'renderContent')) {
                $output = $renderer->renderContent($content, $params);
            }
        }

        return $output;
    }
}