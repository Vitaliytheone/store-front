<?php

namespace common\components\exceptions;

use yii\base\Model;
use yii\web\HttpException;

class FirstValidationErrorHttpException extends HttpException
{
    /**
     * FirstValidationErrorHttpException constructor.
     * @param Model $form
     * @param \Exception|null $previous
     */
    public function __construct(Model $form, \Exception $previous = null)
    {
        $message = null;

        if ($form->hasErrors()) {
            $validationErrors = $form->getFirstErrors();
            $message = reset($validationErrors);
        }

        parent::__construct(400, $message, 400, $previous);
    }
}
