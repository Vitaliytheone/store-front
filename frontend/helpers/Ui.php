<?php

namespace frontend\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidParamException;

class Ui
{
    /**
     * Check if filter is active
     * Allowed `-1` value for dummy `All` filter
     * @param $filterName
     * @param null $filterValue
     * @param string $activeValue Value returned when filter is active
     * @param string $inactiveValue Value returned when filter
     * @return string
     */
    public static function isFilterActive($filterName, $filterValue = null, $activeValue = 'active', $inactiveValue = '')
    {
        $filter = yii::$app->getRequest()->get($filterName, null);

        // Dummy `All` filter
        if ($filterValue === -1) {
            return is_null($filter) ? $activeValue : $inactiveValue;
        }
        return $filter == $filterValue ? $activeValue : $inactiveValue;
    }

    /**
     * Check if filter is present
     * @param $filterName
     * @param string $isPresentValue
     * @param string $isNotPresentValue
     * @return string
     */
    public static function isFilterPresent($filterName, $isPresentValue = 'active', $isNotPresentValue = '')
    {
        return yii::$app->getRequest()->get($filterName) ? $isPresentValue : $isNotPresentValue;
    }


    /**
     * Generates first of the validation error for the model.
     * If there is no validation error, an empty error summary markup will still be generated, but it will be hidden.
     * @param $model
     * @param array $options the tag options in terms of name-value pairs.
     *  - class
     *  — style
     * The rest of the options will be rendered as the attributes of the container tag. The values will
     * be HTML-encoded using [[\yii\helpers\Html::encode()]]. If a value is `null`, the corresponding attribute will not be rendered.
     * @return string the generated single error.
     * @see errorSummaryCssClass
     */
    public static function errorSummary($model, $options = [])
    {
        // Get model first field first validation error
        $errorsSummary = $model->getErrors();
        foreach ($errorsSummary as $fieldErrors) {
            $firstError = Html::encode($fieldErrors[0]);
            break;
        }

        if (empty($firstError)) {
            // still render the placeholder for client-side validation use
            $content = '<ul></ul>';
            $options['style'] = isset($options['style']) ? rtrim($options['style'], ';') . '; display:none' : 'display:none';
        } else {
            $content = '<ul><li>' . $firstError . '</li></ul>';
        }
        return Html::tag('div',  $content , $options);
    }
}