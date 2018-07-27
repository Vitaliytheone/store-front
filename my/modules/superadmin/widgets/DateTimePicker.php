<?php

namespace my\modules\superadmin\widgets;

use my\modules\superadmin\controllers\CustomController;
use yii\base\Model;
use yii\base\Widget;

/**
 * Class DateTimePicker
 * @package my\modules\superadmin\widgets
 */
class DateTimePicker extends Widget
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var CustomController
     */
    public $context;

    /**
     * @var string
     */
    public $format = 'YYYY-MM-DD HH:mm:ss';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->context->addModule('superadminDatetimepickerWidgetController');

        return $this->render('_date_time_picker', [
            'model' => $this->model,
            'attribute' => $this->attribute,
            'context' => $this->context,
            'format' => $this->format,
        ]);
    }
}