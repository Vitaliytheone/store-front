<?php

namespace common\components\filters;


use yii\base\ActionFilter;
use Yii;

/**
 * DisableDomainValidation
 *
 * Example:
 *
 * public function behaviors()
 * {
 *     return => [
 *         [
 *             'class' => DisableDomainValidation::class,
 *             'only' => ['view', 'index'],
 *         ],
 *     ];
 * }
 *
 * Class DisableDomainValidation
 * @package common\components\filters
 */
class DisableDomainValidation extends ActionFilter
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (!empty(Yii::$app->controller->enableDomainValidation)) {
            Yii::$app->controller->enableDomainValidation = false;
        }
        return parent::init();
    }
}