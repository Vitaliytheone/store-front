<?php

namespace control_panel\components;

use Yii;
use yii\bootstrap\Html;

/**
 * Class ActiveForm
 * @package control_panel\components
 */
class ActiveForm extends \yii\bootstrap\ActiveForm
{
    public $errorCssClass = '';

    public $enableAjaxValidation = false;

    public $enableClientValidation = false;

    public $errorSummaryCssClass = 'error-summary alert alert-danger';

    public $inputTemplate = '{label}{input}';

    public $enableError = false;

    /**
     * Generates a summary of the validation errors.
     * If there is no validation error, an empty error summary markup will still be generated, but it will be hidden.
     * @param Model|Model[] $models the model(s) associated with this form.
     * @param array $options the tag options in terms of name-value pairs. The following options are specially handled:
     *
     * - `header`: string, the header HTML for the error summary. If not set, a default prompt string will be used.
     * - `footer`: string, the footer HTML for the error summary.
     *
     * The rest of the options will be rendered as the attributes of the container tag. The values will
     * be HTML-encoded using [[\yii\helpers\Html::encode()]]. If a value is `null`, the corresponding attribute will not be rendered.
     * @return string the generated error summary.
     * @see errorSummaryCssClass
     */
    public function errorSummary($models, $options = [])
    {
        Html::addCssClass($options, $this->errorSummaryCssClass);

        $encode = $this->encodeErrorSummary;

        $error = static::firstError($models, $encode);

        if (empty($error)) {
            // still render the placeholder for client-side validation use
            $content = '';
            $options['class'] = isset($options['class']) ? $options['class'] . ' hidden' : 'hidden';
        } else {
            $content = $error;
        }

        return Html::tag('div', $content, $options);
    }

    /**
     * Get first summary error content
     * @param Model|Model[] $models the model(s) associated with this form.
     * @param bool $encode
     * @return string
     */
    public static function firstError($models, $encode = true)
    {
        $error = '';
        if (!is_array($models)) {
            $models = [$models];
        }
        foreach ($models as $model) {
            /* @var $model Model */
            foreach ($model->getErrors() as $errors) {
                foreach ($errors as $error) {
                    $error = $encode ? Html::encode($error) : $error;
                    break;
                }
                break;
            }
            break;
        }

        return $error;
    }

    /**
     * Show success flash alert
     * @return string|void
     */
    public function successMessage()
    {
        if (!Yii::$app->session->hasFlash('success')) {
            return;
        }

        $message = Yii::$app->session->getFlash('success');

        if (is_array($message)) {
            $message = array_shift($message);
        }

        $content = Html::button('<span aria-hidden="true">×</span>', [
            'class' => 'close',
            'data-dismiss' => 'alert',
            'aria-label' => 'Close'
        ]);

        $content .= $message;

        return Html::tag('div', $content, [
            'class' => 'alert alert-success'
        ]);
    }
}