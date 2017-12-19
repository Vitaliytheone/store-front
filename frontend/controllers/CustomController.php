<?php

namespace frontend\controllers;

use common\components\MainController;
use frontend\models\search\CartSearch;
use frontend\modules\admin\components\Url;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use Yii;
use yii\web\View;

/**
 * Custom controller for the `admin` module
 */
class CustomController extends MainController
{
    public $layout = '@frontend/views/site/layout.php';

    protected $_globalParams;

    public $customJs = [];

    /**
     * @inheritdoc
     */
    /*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ]
                ],
            ],
        ];
    }*/

    /**
     * Activate js module
     * @param string $name
     * @param array $options
     */
    public function addModule($name, $options = [])
    {
        $this->customJs[] = 'window.modules.' . $name . ' = ' . json_encode($options) . ';';
    }

    public function beforeAction($action)
    {
        $this->addModule('frontendLayout');

        return parent::beforeAction($action);
    }

    /**
     * Get previous url
     * @return null|string
     */
    public function getGoBackUrl()
    {
        $prev = Url::previous();
        return (Url::to('') != $prev) ? $prev : Url::toRoute('/');
    }

    public function getViewPath()
    {
        return Yii::getAlias('@frontend/views/site');
    }

    /**
     * Get global params
     * @return array
     */
    protected function _getGlobalParams()
    {
        if ($this->_globalParams) {
            return $this->_globalParams;
        }

        $cartItems = new CartSearch();
        $cartItems->setStore(Yii::$app->store->getInstance());

        $endContent = '';

        if (YII_ENV_DEV) {
            ob_start();
            $this->getView()->trigger(View::EVENT_END_BODY);
            $endContent = ob_get_contents();
            ob_end_clean();
        }

        foreach ($this->customJs as $js) {
            $scripts[] = [
                'code' => $js
            ];
        }

        $this->_globalParams = [
            'csrfname' => Yii::$app->getRequest()->csrfParam,
            'csrftoken' => Yii::$app->getRequest()->getCsrfToken(),
            'site' => [
                'cart_count' => $cartItems->getCount(),
                'custom_footer' => $endContent,
                'scripts' => $scripts
            ]

        ];

        return $this->_globalParams;
    }

    /**
     * Renders a view and applies layout if available.
     *
     * The view to be rendered can be specified in one of the following formats:
     *
     * - [path alias](guide:concept-aliases) (e.g. "@app/views/site/index");
     * - absolute path within application (e.g. "//site/index"): the view name starts with double slashes.
     *   The actual view file will be looked for under the [[Application::viewPath|view path]] of the application.
     * - absolute path within module (e.g. "/site/index"): the view name starts with a single slash.
     *   The actual view file will be looked for under the [[Module::viewPath|view path]] of [[module]].
     * - relative path (e.g. "index"): the actual view file will be looked for under [[viewPath]].
     *
     * To determine which layout should be applied, the following two steps are conducted:
     *
     * 1. In the first step, it determines the layout name and the context module:
     *
     * - If [[layout]] is specified as a string, use it as the layout name and [[module]] as the context module;
     * - If [[layout]] is null, search through all ancestor modules of this controller and find the first
     *   module whose [[Module::layout|layout]] is not null. The layout and the corresponding module
     *   are used as the layout name and the context module, respectively. If such a module is not found
     *   or the corresponding layout is not a string, it will return false, meaning no applicable layout.
     *
     * 2. In the second step, it determines the actual layout file according to the previously found layout name
     *    and context module. The layout name can be:
     *
     * - a [path alias](guide:concept-aliases) (e.g. "@app/views/layouts/main");
     * - an absolute path (e.g. "/main"): the layout name starts with a slash. The actual layout file will be
     *   looked for under the [[Application::layoutPath|layout path]] of the application;
     * - a relative path (e.g. "main"): the actual layout file will be looked for under the
     *   [[Module::layoutPath|layout path]] of the context module.
     *
     * If the layout name does not contain a file extension, it will use the default one `.php`.
     *
     * @param string $view the view name.
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * These parameters will not be available in the layout.
     * @return string the rendering result.
     * @throws InvalidParamException if the view file or the layout file does not exist.
     */
    public function render($view, $params = [])
    {
        $params = array_merge($this->_getGlobalParams(), $params);

        return parent::render($view, $params); // TODO: Change the autogenerated stub
    }

    /**
     * Renders a static string by applying a layout.
     * @param string $content the static string being rendered
     * @return string the rendering result of the layout with the given static string as the `$content` variable.
     * If the layout is disabled, the string will be returned back.
     * @since 2.0.1
     */
    public function renderContent($content)
    {
        $layoutFile = $this->findLayoutFile($this->getView());
        if ($layoutFile !== false) {
            return $this->getView()->renderFile($layoutFile, array_merge($this->_getGlobalParams(), [
                'content' => $content,
            ]), $this);
        }

        return $content;
    }
}
