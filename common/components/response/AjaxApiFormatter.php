<?php

namespace common\components\response;

use Yii;
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
        $data = [];

        if ($response->isSuccessful) {
            $data += [
                'success' => true,
                'error_message' => null,
                'data' => $response->data,
            ];
        } else {

            $errorMessage = ArrayHelper::getValue($response->data, 'message', ArrayHelper::getValue($response->data, 'name', $this->defaultErrorMessage));

            $data += [
                'success' => false,
                'error_message' => $errorMessage,
                'data' => null,
            ];

            if (YII_ENV_DEV) {
                /** @var \Exception $exception */
                $exception = Yii::$app->errorHandler->exception;

                if ($exception) {
                    $data += [
                        '_exception' => [
                            'message' => $exception->getMessage(),
                            'code' => $exception->getCode(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'trace' => $exception->getTraceAsString(),
                        ],
                    ];
                }
            }
        }

        $response->data = $data;

        $options = $this->encodeOptions;
        if ($this->prettyPrint) {
            $options |= JSON_PRETTY_PRINT;
        }

        $response->content = Json::encode($response->data, $options);
    }
}
