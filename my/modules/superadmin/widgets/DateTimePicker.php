<?php

namespace my\modules\superadmin\widgets;

use my\components\ActiveForm;
use yii\base\Widget;

/**
 * Class DateTimePicker
 * @package my\modules\superadmin\widgets
 */
class DateTimePicker extends Widget
{
    public $model;
    public $attribute;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('_date_time_picker', [
            'model' => $this->model,
            'attribute' => $this->attribute,
        ]);
    }
}