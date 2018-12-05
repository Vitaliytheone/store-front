<?php

namespace common\components\filters;


use yii\base\ActionFilter;
use Yii;

/**
 * DisableCsrfToken is an action filter that disables CSRF token validation
 *
 * Example:
 *
 * public function behaviors()
 * {
 *     return => [
 *         [
 *             'class' => DisableCsrfFilter::class,
 *             'only' => ['view', 'index'],
 *         ],
 *     ];
 * }
 *
 * Class DisableCsrfToken
 * @package common\components\filters
 */
class DisableCsrfToken extends ActionFilter
{

    /**
     * {@inheritdoc}
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        Yii::$app->controller->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
}