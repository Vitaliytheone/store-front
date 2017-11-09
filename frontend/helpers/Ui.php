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
            $content = '<div></div>';
            $options['style'] = isset($options['style']) ? rtrim($options['style'], ';') . '; display:none' : 'display:none';
        } else {
            $content = '<div>' . $firstError . '</div>';
        }
        return Html::tag('div',  $content , $options);
    }

    /**
     * Renders list models summary data.
     *
     * @param $dataProvider yii\data\BaseDataProvider;
     * @param $options array the HTML attributes for the summary of the list view.
     * The "tag" element specifies the tag name of the summary element and defaults to "div".
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     *
     * @param $summary  string the HTML content to be displayed as the summary of the list view.
     * If you do not want to show the summary, you may set it with an empty string.
     *
     * The following tokens will be replaced with the corresponding values:
     *
     * - `{begin}`: the starting row number (1-based) currently being displayed
     * - `{end}`: the ending row number (1-based) currently being displayed
     * - `{count}`: the number of rows currently being displayed
     * - `{totalCount}`: the total number of rows available
     * - `{page}`: the page number (1-based) current being displayed
     * - `{pageCount}`: the number of pages available
     *
     * @return string
     */
    public static function listSummary($dataProvider, $options = ['class' => 'summary'], $summary = null) {
    $count = $dataProvider->getCount();
    if ($count <= 0) {
        return '';
    }
    $tag = ArrayHelper::remove($options, 'tag', 'div');
    if (($pagination = $dataProvider->getPagination()) !== false) {
        $totalCount = $dataProvider->getTotalCount();
        $begin = $pagination->getPage() * $pagination->pageSize + 1;
        $end = $begin + $count - 1;
        if ($begin > $end) {
            $begin = $end;
        }
        $page = $pagination->getPage() + 1;
        $pageCount = $pagination->pageCount;
        if (($summaryContent = $summary) === null) {
            return Html::tag($tag, Yii::t('yii', '{begin, number} to {end, number} of {totalCount, number}', [
                'begin' => $begin,
                'end' => $end,
                'count' => $count,
                'totalCount' => $totalCount,
                'page' => $page,
                'pageCount' => $pageCount,
            ]), $options);
        }
    } else {
        $begin = $page = $pageCount = 1;
        $end = $totalCount = $count;
        if (($summaryContent = $summary) === null) {
            return Html::tag($tag, Yii::t('yii', 'Total <b>{count, number}</b> {count, plural, one{item} other{items}}.', [
                'begin' => $begin,
                'end' => $end,
                'count' => $count,
                'totalCount' => $totalCount,
                'page' => $page,
                'pageCount' => $pageCount,
            ]), $options);
        }
    }

    return Yii::$app->getI18n()->format($summaryContent, [
        'begin' => $begin,
        'end' => $end,
        'count' => $count,
        'totalCount' => $totalCount,
        'page' => $page,
        'pageCount' => $pageCount,
    ], Yii::$app->language);
}

}