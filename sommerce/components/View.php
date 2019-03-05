<?php

namespace sommerce\components;

use Yii;
use yii\base\ViewRenderer;

/**
 * Class View
 * @package sommerce\components
 */
class View extends \yii\web\View {

    /**
     * Render content
     * @param string $content
     * @param array $params
     * @param array $endContent
     * @param array $startHeadContent
     * @param string $ext
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderContent($content, $params = [], $endContent = [], $startHeadContent = [], $ext = 'twig'): string
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

        if (!empty($endContent)) {
            $output = str_ireplace('</body>',  implode("\r\n", $endContent) . '</body>', $output);
        }

        if (!empty($startHeadContent)) {
            $output = str_ireplace('</head>',  implode("\r\n", $startHeadContent) . '</head>', $output);
        }

        return $output;
    }
}