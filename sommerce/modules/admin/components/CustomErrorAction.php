<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace sommerce\modules\admin\components;

use Yii;
use yii\web\ErrorAction;

/**
 * ErrorAction displays application errors using a specified view.
 *
 * To use ErrorAction, you need to do the following steps:
 *
 * First, declare an action of ErrorAction type in the `actions()` method of your `SiteController`
 * class (or whatever controller you prefer), like the following:
 *
 * ```php
 * public function actions()
 * {
 *     return [
 *         'error' => ['class' => 'yii\web\ErrorAction'],
 *     ];
 * }
 * ```
 *
 * Then, create a view file for this action. If the route of your error action is `site/error`, then
 * the view file should be `views/site/error.php`. In this view file, the following variables are available:
 *
 * - `$name`: the error name
 * - `$message`: the error message
 * - `$exception`: the exception being handled
 *
 * Finally, configure the "errorHandler" application component as follows,
 *
 * ```php
 * 'errorHandler' => [
 *     'errorAction' => 'site/error',
 * ]
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Dmitry Naumenko <d.naumenko.a@gmail.com>
 * @since 2.0
 */
class CustomErrorAction extends ErrorAction
{
    /**
     * @var string the layout file to be rendered. If not set, it will take the current controller value
     */
    public $layout;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->layout) {
            $this->controller->layout = $this->layout;
        }

        $this->exception = $this->findException();

        if ($this->defaultMessage === null) {
            $this->defaultMessage = Yii::t('yii', 'An internal server error occurred.');
        }

        if ($this->defaultName === null) {
            $this->defaultName = Yii::t('yii', 'Error');
        }
    }

    /**
     * Runs the action.
     *
     * @return string result content
     */
    public function run()
    {
        Yii::$app->getResponse()->setStatusCodeByException($this->exception);

        if (Yii::$app->getRequest()->getIsAjax()) {
            return $this->renderAjaxResponse();
        }

        // Redirect guest user to homepage
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
        }

        return $this->renderHtmlResponse();
    }
}
