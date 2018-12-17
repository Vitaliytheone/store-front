<?php
namespace sommerce\components;

use sommerce\helpers\ThemesHelper;
use Yii;
use yii\base\InvalidCallException;
use yii\base\ViewContextInterface;
use yii\base\ViewRenderer;
use yii\web\NotFoundHttpException;

/**
 * Class View
 * @package sommerce\components
 */
class View extends \yii\web\View {

    /**
     * Finds the view file based on the given view name.
     * @param string $view the view name or the [path alias](guide:concept-aliases) of the view file. Please refer to [[render()]]
     * on how to specify this parameter.
     * @param object $context the context to be assigned to the view and can later be accessed via [[context]]
     * in the view. If the context implements [[ViewContextInterface]], it may also be used to locate
     * the view file corresponding to a relative view name.
     * @return string the view file path. Note that the file may not exist.
     * @throws InvalidCallException if a relative view name is given while there is no active context to
     * determine the corresponding view file.
     */
    protected function findViewFile($view, $context = null)
    {
        if (pathinfo($view, PATHINFO_EXTENSION) !== 'twig') {
            return parent::findViewFile($view, $context);
        }

        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = Yii::getAlias($view);
        } elseif (strncmp($view, '//', 2) === 0) {
            // e.g. "//layouts/main"
            $file = Yii::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            $file = $this->getThemeViewFile($view);
        } elseif (strncmp($view, '/', 1) === 0) {
            $file = $this->getThemeViewFile($view);
        } elseif ($context instanceof ViewContextInterface) {
            $file = $this->getThemeViewFile($view);
        } elseif (($currentViewFile = $this->getViewFile()) !== false) {
            $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;
        } else {
            throw new InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }

        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

    /**
     * Get view themes file path
     * @param string $view
     * @return string
     */
    public function getThemeViewFile($view)
    {
        $view = ThemesHelper::getView($view);

        if (!$view) {
            if (pathinfo($view, PATHINFO_EXTENSION) == 'twig') {
                throw new NotFoundHttpException();
            }
            throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
        }

        $viewsPath = Yii::getAlias('@sommerce' . DIRECTORY_SEPARATOR . 'views');

        return $viewsPath . $view;
    }

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