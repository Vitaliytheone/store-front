<?php

namespace sommerce\controllers;

use common\models\sommerces\StorePaymentMethods;
use sommerce\components\filters\IntegrationsFilter;
use sommerce\components\View;
use sommerce\helpers\AssetsHelper;
use sommerce\models\search\NavigationSearch;
use sommerce\modules\admin\components\Url;
use Yii;
use yii\base\Exception;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * Custom controller for the Sommerce
 */
class CustomController extends CommonController
{
    /** @var string  */
    public $pageTitle;

    /**
     * Header meta description
     * You can redefine this value
     * @var
     */
    public $seoDescription;

    /**
     * Header meta keywords
     * You can redefine this value
     * @var
     */
    public $seoKeywords;

    /**
     * @var array
     */
    public $endContent;

    /**
     * @var array
     */
    public $startHeadContent;

    /**
     * @var string
     */
    public $layout = '@sommerce/views/site/layout.php';

    /**
     * @var
     */
    protected $_globalParams;

    /**
     * @var array
     */
    public $customJs = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $store = Yii::$app->store->getInstance();

        $this->layout = "layout.twig";
        Yii::$app->language = $store->language;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'integrations' => IntegrationsFilter::class,
        ]);
    }

    /**
     * Activate js module
     * @param string $name
     * @param array $options
     */
    public function addModule($name, $options = [])
    {
        $this->customJs[] = 'window.modules.' . $name . ' = ' . json_encode($options) . ';';
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        // Redirect frozen store to `frozen page`
        if ($this->store->isInactive() && $action->id !== 'frozen') {
            $this->redirect(Url::to('/frozen'));
        }

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
        return Yii::getAlias('@sommerce/views/site');
    }

    /**
     * Get global params
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected function _getGlobalParams()
    {
        if ($this->_globalParams) {
            return $this->_globalParams;
        }

        $this->endContent = [];

        if (!empty($this->customJs)) {

            $appJs = [
                '/js/frontend.js',
            ];

            foreach ($appJs as $src) {
                $this->endContent[] = Html::script('', ['src' => $src, 'type' => 'text/javascript']);
            }

            $this->endContent[] = Html::script(implode("\r\n", $this->customJs), ['type' => 'text/javascript']);
        }

        if (YII_ENV_DEV) {
            ob_start();
            $this->getView()->trigger(View::EVENT_END_BODY);
            $this->endContent[] = ob_get_contents();
            ob_end_clean();
        }
        $search =  new NavigationSearch();
        $search->setStore($this->store);

        $this->_globalParams = [
            'csrfname' => Yii::$app->getRequest()->csrfParam,
            'csrftoken' => Yii::$app->getRequest()->getCsrfToken(),
            'site' => [
                'captcha_key' => Yii::$app->params['reCaptcha.siteKey'],
            ],
            'page' => [
                'site.captcha_key' => Yii::$app->params['reCaptcha.siteKey'],
                'site.paymentMethods' => StorePaymentMethods::getActiveMethods($this->store->id),
                'site.url' => trim(Yii::$app->getRequest()->url, '/'),
                'favicon' => $this->store->favicon,
                'logo' => $this->store->logo,
                'story_domain' => Yii::$app->getRequest()->getHostName(),
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
     * @param array $params he parameters (name-value pairs) that should be made available in the view.
     * These parameters will not be available in the layout.
     * @return string the rendering result.
     * @throws \yii\base\InvalidConfigException if the view file or the layout file does not exist.
     */
    public function render($view, $params = [])
    {
        $params = array_merge($this->_getGlobalParams(), $params);

        return parent::render($view, $params);
    }

    /**
     * Render content partial without applying layout
     * @param $content
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function renderContentPartial($content, $params)
    {
        $renderer = $this->getView();

        if (!method_exists($renderer, 'renderContent')) {
            throw new Exception('This View does not support renderContent method!');
        }

        $content = $renderer->renderContent($content, $params);

        return $renderer->renderContent($content, $params);
    }


}
