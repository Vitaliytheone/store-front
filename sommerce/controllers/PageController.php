<?php

namespace sommerce\controllers;

use sommerce\helpers\PageFilesHelper;
use sommerce\helpers\PagesHelper;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Page controller
 */
class PageController extends CustomController
{
    /**
     * Error action
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionError()
    {
        $content = file_get_contents(self::getTwigView('404'));

        return $this->renderTwigContent($content, [], false);
    }

    /**
     * Render page by url
     * @param string $url
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionIndex($url = 'index')
    {
        $page = PagesHelper::getPage($url);

        if (!$page) {
            throw new NotFoundHttpException("Page by url '{$url}' not found");
        }

        $this->pageTitle = $page['seo_title'];
        $this->seoKeywords = $page['seo_keywords'];
        $this->seoDescription = $page['seo_description'];

        $content = $page['twig'] ?? '';

        return $this->renderTwigContent($content);
    }

    /**
     * Render page styles by url
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionStyles($name = 'styles.css')
    {
        $files = PageFilesHelper::getFileByName($name);

        if (empty($files)) {
            throw new NotFoundHttpException("File {$name} not found");
        }

        return Yii::$app->response->sendContentAsFile($files['content'], $name, [
            'mimeType' => 'text/css;charset=UTF-8',
            'inline' => true,
        ]);
    }

    /**
     * Render page scripts by url
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionScripts($name = 'scripts.js')
    {
        $files = PageFilesHelper::getFileByName($name);

        if (empty($files)) {
            throw new NotFoundHttpException("File {$name} not found");
        }

        return Yii::$app->response->sendContentAsFile($files['content'], $name, [
            'mimeType' => 'text/javascript;charset=UTF-8',
            'inline' => true,
        ]);
    }

    /**
     * Renders a static string by applying a layout.
     * @param string $content the static string being rendered
     * @param array $params
     * @param boolean $layout
     * @return string the rendering result of the layout with the given static string as the `$content` variable.
     * If the layout is disabled, the string will be returned back.
     * @throws \yii\base\InvalidConfigException
     * @since 2.0.1
     */
    public function renderTwigContent($content, $params = [], $layout = true)
    {
        $renderer = $this->getView();

        if (!method_exists($renderer, 'renderContent')) {
            return '';
        }

        $content = $renderer->renderContent($content, $params);

        if (!$layout) {
            return $content;
        }

        $layoutFile = self::getLayout();

        if ($layoutFile === false) {
            return $content;
        }

        return $renderer->renderContent($layoutFile, array_merge($this->_getGlobalParams(), [
            'page_content' => $content,
        ]));
    }

    /**
     * Get current layout from file if exist
     * @param string $name
     * @return mixed|null
     */
    public static function getLayout($name = 'layout.twig')
    {
        $layouts = file_get_contents(self::getTwigView($name));

        if (empty($layouts)) {
            $layouts = PageFilesHelper::getFileByName($name);
            $layouts = $layouts['content'];
        }

        return $layouts;
    }

    /**
     * Get view path use view name
     * @param string $view
     * @param string $defaultExtension
     * @return string|null
     */
    public static function getTwigView($view, $defaultExtension = 'twig')
    {
        $view = ltrim($view, '/');
        if (strpos($view, '.twig') === false && strpos($view, '.php') === false) {
            $view = "{$view}.{$defaultExtension}";
        }

        $sp = DIRECTORY_SEPARATOR;
        $viewsPath = Yii::getAlias('@sommerce' . $sp . 'views');
        $rootPath = $viewsPath . $sp . $view;
        $pagePath = $viewsPath . $sp . 'page' . $sp . $view;

        if (is_file($rootPath) || is_file($rootPath . '.' . $defaultExtension) || is_file($rootPath . '.php')) {
            return $rootPath;
        }
        if (is_file($pagePath) || is_file($pagePath . '.' . $defaultExtension) || is_file($pagePath . '.php')) {
            return $pagePath;
        }

        return null;
    }
}
