<?php
namespace common\components\twig;

use Yii;
use yii\base\View;
use yii\base\ViewRenderer as BaseViewRenderer;
use Twig_Environment;
use Twig_Loader_Array;
use Twig_Loader_Filesystem;
use yii\helpers\FileHelper;
use Twig_LoaderInterface;

/**
 * Class ViewRenderer
 * @package app\components\twig
 */
class ViewRenderer extends BaseViewRenderer
{
    /**
     * @var string the directory or path alias pointing to where Twig cache will be stored. Set to false to disable
     * templates cache.
     */
    public $cachePath = '@runtime/Twig/cache';

    /**
     * @var array Twig options.
     * @see http://twig.sensiolabs.org/doc/api.html#environment-options
     */
    public $options = [];

    /**
     * @var \Twig_Environment twig environment object that renders twig templates
     */
    public $twig;

    /**
     * @var \Twig_Environment twig environment object that renders twig templates
     */
    public $twigContent;

    /**
     * @var string twig namespace to use in templates
     * @since 2.0.5
     */
    public $twigViewsNamespace = Twig_Loader_Filesystem::MAIN_NAMESPACE;

    /**
     * @var string twig namespace to use in modules templates
     * @since 2.0.5
     */
    public $twigModulesNamespace = 'modules';

    /**
     * @var string twig namespace to use in widgets templates
     * @since 2.0.5
     */
    public $twigWidgetsNamespace = 'widgets';

    /**
     * @var array twig fallback paths
     * @since 2.0.5
     */
    public $twigFallbackPaths = [];

    /**
     * @var array Global variables.
     * Keys of the array are names to call in template, values are scalar or objects or names of static classes.
     * Example: `['html' => ['class' => '\yii\helpers\Html'], 'debug' => YII_DEBUG]`.
     * In the template you can use it like this: `{{ html.a('Login', 'site/login') | raw }}`.
     */
    public $globals = [];

    /**
     * @var Extension
     */
    public $extension = Extension::class;

    public function init()
    {
        $this->getTwig();
    }

    /**
     * @var Twig_LoaderInterface
     */
    protected $loader;

    /**
     * @return Twig_Environment
     */
    protected function getTwig()
    {
        if (null !== $this->twig) {
            return $this->twig;
        }

        $options = [
            'charset' => Yii::$app->charset,
        ];

        if ($this->cachePath) {
            $path = Yii::getAlias($this->cachePath);

            if (!is_dir($path)) {
                FileHelper::createDirectory($path);
                chmod($path, 0777);
            }
            $options['cache'] = new TwigCache($path);
        }

        $this->twig = new Twig_Environment($this->getLoader(), array_merge($options, $this->options));

        $this->addExtensions($this->twig, [
            new $this->extension($this->options),
        ]);

        return $this->twig;
    }

    /**
     * Render content by string template
     * @param string $content
     * @param array $params
     * @return string
     */
    public function renderContent($content, $params = [])
    {
        $twig = $this->getTwigContent();
        $twig->setLoader($this->getLoader());

        return $twig->createTemplate($content)->render($params);
    }

    /**
     * @return Twig_LoaderInterface
     */
    public function getLoader()
    {
        if (null == $this->loader) {
            $this->loader = new Twig_Loader_Filesystem();
        }

        return $this->loader;
    }

    /**
     * @return Twig_Environment
     */
    protected function getTwigContent()
    {
        if (null !== $this->twigContent) {
            return $this->twigContent;
        }

        $options = [
            'charset' => Yii::$app->charset,
        ];

        $this->twigContent = new Twig_Environment(new Twig_Loader_Array(), array_merge($options, $this->options));

        $this->addExtensions($this->twigContent, [
            new $this->extension($this->options),
        ]);

        return $this->twigContent;
    }

    /**
     * Renders a view file.
     *
     * This method is invoked by [[View]] whenever it tries to render a view.
     * Child classes must implement this method to render the given view file.
     *
     * @param View $view the view object used for rendering the file.
     * @param string $file the view file.
     * @param array $params the parameters to be passed to the view file.
     *
     * @return string the rendering result
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($view, $file, $params)
    {
        $this->twig->addGlobal('this', $view);
        $loader = new Twig_Loader_Filesystem(dirname($file));
        if ($view instanceof View) {
            $this->addFallbackPaths($loader, $view->theme);
        }
        $this->addAliases($loader, Yii::$aliases);
        $this->twig->setLoader($loader);

        // Add custom scripts/styles/code before </html> tag
        $content = $this->twig->render(pathinfo($file, PATHINFO_BASENAME), $params);
        if (!empty($view->context->endContent)) {
            $content = preg_replace("/\<\/html[^\w\d]*\>/uis", "\r\n" . implode("\r\n", $view->context->endContent) . "\r\n</html>", $content, 1);
        }

        if (!empty($view->context->startHeadContent)) {
            $content = preg_replace("/\<head[^\w\d]*\>/uis", "<head>\r\n" . implode("\r\n", $view->context->startHeadContent) . "\r\n", $content, 1);
        }

        return $content;
    }

    /**
     * Adds aliases
     *
     * @param \Twig_Loader_Filesystem $loader
     * @param array $aliases
     */
    protected function addAliases($loader, $aliases)
    {
        foreach ($aliases as $alias => $path) {
            if (is_array($path)) {
                $this->addAliases($loader, $path);
            } elseif (is_string($path) && is_dir($path)) {
                $loader->addPath($path, substr($alias, 1));
            }
        }
    }

    /**
     * Adds custom extensions
     * @param Twig_Environment $twig
     * @param array $extensions @see self::$extensions
     */
    public function addExtensions(&$twig, $extensions)
    {
        foreach ($extensions as $extName) {
            $twig->addExtension(is_object($extName) ? $extName : Yii::createObject($extName));
        }
    }

    /**
     * @return \Twig_ExtensionInterface[]
     */
    public function getExtensions()
    {
        return $this->twig->getExtensions();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Adds fallback paths to twig loader
     *
     * @param \Twig_Loader_Filesystem $loader
     * @param \yii\base\Theme|null $theme
     * @since 2.0.5
     */
    protected function addFallbackPaths($loader, $theme)
    {
        foreach ($this->twigFallbackPaths as $namespace => $path) {
            $path = Yii::getAlias($path);
            if (!is_dir($path)) {
                continue;
            }
            if (is_string($namespace)) {
                $loader->addPath($path, $namespace);
            } else {
                $loader->addPath($path);
            }
        }
        if ($theme instanceOf \yii\base\Theme && is_array($theme->pathMap)) {
            $pathMap = $theme->pathMap;
            if (isset($pathMap['@app/views'])) {
                foreach ((array)$pathMap['@app/views'] as $path) {
                    $path = Yii::getAlias($path);
                    if (is_dir($path)) {
                        $loader->addPath($path, $this->twigViewsNamespace);
                    }
                }
            }
            if (isset($pathMap['@app/modules'])) {
                foreach ((array)$pathMap['@app/modules'] as $path) {
                    $path = Yii::getAlias($path);
                    if (is_dir($path)) {
                        $loader->addPath($path, $this->twigModulesNamespace);
                    }
                }
            }
            if (isset($pathMap['@app/widgets'])) {
                foreach ((array)$pathMap['@app/widgets'] as $path) {
                    $path = Yii::getAlias($path);
                    if (is_dir($path)) {
                        $loader->addPath($path, $this->twigWidgetsNamespace);
                    }
                }
            }
        }
        $defaultViewsPath = Yii::getAlias('@app/views');
        if (is_dir($defaultViewsPath)) {
            $loader->addPath($defaultViewsPath, $this->twigViewsNamespace);
        }
        $defaultModulesPath = Yii::getAlias('@app/modules');
        if (is_dir($defaultModulesPath)) {
            $loader->addPath($defaultModulesPath, $this->twigModulesNamespace);
        }
        $defaultWidgetsPath = Yii::getAlias('@app/widgets');
        if (is_dir($defaultWidgetsPath)) {
            $loader->addPath($defaultWidgetsPath, $this->twigWidgetsNamespace);
        }
    }
}