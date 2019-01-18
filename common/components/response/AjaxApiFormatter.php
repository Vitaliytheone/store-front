<?php

namespace common\components\response;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsonResponseFormatter;

/**
 * Class AjaxApiFormatter
 * uses for build formatted ajax-api responses
 *
 * @package common\components\ajax_response
 */
class AjaxApiFormatter extends JsonResponseFormatter
{
    /**
     * Default exception message
     * @var string
     */
    protected $defaultErrorMessage = 'Internal error!';

    /**
     * @param \yii\web\Response $response
     */
    protected function formatJson($response)
    {
        if ($response->data !== null) {
            $options = $this->encodeOptions;
            if ($this->prettyPrint) {
                $options |= JSON_PRETTY_PRINT;
            }

            if ($response->isSuccessful) {
                $data = [
                    'success' => true,
                    'error_message' => null,
                    'data' => $response->data,
                ];
            } else {

                $errorMessage = ArrayHelper::getValue($response->data, 'message', ArrayHelper::getValue($response->data, 'name', $this->defaultErrorMessage));

                $data = [
                    'success' => false,
                    'error_message' => $errorMessage,
                    'data' => null,
                ];
            }

            $response->data = $data;

            $response->content = Json::encode($response->data, $options);
        }
    }
}
