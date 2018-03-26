<?php
namespace sommerce\modules\admin\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * LinkPager displays a list of hyperlinks that lead to different pages of target.
 *
 * LinkPager works with a [[Pagination]] object which specifies the total number
 * of pages and the current page number.
 *
 * Note that LinkPager only generates the necessary HTML markups. In order for it
 * to look like a real pager, you should provide some CSS styles for it.
 * With the default configuration, LinkPager should look good using Twitter Bootstrap CSS framework.
 *
 * For more details and usage information on LinkPager, see the [guide article on pagination](guide:output-pagination).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CustomLinkPager extends \yii\widgets\LinkPager
{
    /**
     * Default pagination settings
     * You can override it
     * @var array
     */
    private static $_defaultSettings = [
        'maxButtonCount' => 10,

        'disableCurrentPageButton' => false,
        'hideOnSinglePage' => true,

        'activePageCssClass' => 'm-datatable__pager-link-number m-datatable__pager-link--active',
        'disabledPageCssClass' => 'm-datatable__pager-link--disabled',

        'firstPageCssClass' => 'm-datatable__pager-link--first',
        'firstPageLabel' => '<i class="la la-angle-double-left"></i>',

        'lastPageCssClass' => 'm-datatable__pager-link--last',
        'lastPageLabel' => '<i class="la la-angle-double-right"></i>',

        'prevPageCssClass' => 'm-datatable__pager-link--prev',
        'prevPageLabel' => '<i class="la la-angle-left"></i>',

        'nextPageCssClass' => 'm-datatable__pager-link--next',
        'nextPageLabel' => '<i class="la la-angle-right"></i>',

        'pageCssClass' => 'm-datatable__pager-link-number',

        'options' => ['class' => 'm-datatable__pager-nav'],
        'linkOptions' => ['class' => 'm-datatable__pager-link'],
    ];

    /**
     * Creates a widget instance and runs it.
     * The widget rendering result is returned by this method.
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @return string the rendering result of the widget.
     * @throws \Exception
     */
    public static function widget($config = [])
    {
        ob_start();
        ob_implicit_flush(false);
        try {
            $config = array_merge(static::$_defaultSettings, $config);

            /* @var $widget yii\base\Widget */
            $config['class'] = get_called_class();
            $widget = Yii::createObject($config);
            $out = '';
            if ($widget->beforeRun()) {
                $result = $widget->run();
                $out = $widget->afterRun($result);
            }
        } catch (\Exception $e) {
            // close the output buffer opened above if it has not been closed already
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $e;
        }

        return ob_get_clean() . $out;
    }


    /**
     * Renders a page button.
     * You may override this method to customize the generation of page buttons.
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param string $class the CSS class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     * @return string the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);
        $options['data-page'] = $page;

        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
        }
        return Html::tag($linkWrapTag, Html::a($label, $this->pagination->createUrl($page), $options), []);
    }
}
