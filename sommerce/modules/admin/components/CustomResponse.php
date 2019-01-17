<?php

namespace sommerce\modules\admin\components;

use Yii;
use yii\web\Response;

/**
 * Class CustomResponse
 * @package sommerce\modules\admin\components\CustomResponse
 */
class CustomResponse extends Response
{
    public function send()
    {
        if ($this->isSent) {
            return;
        }

        if ($this->format === Response::FORMAT_JSON) {
            $this->data = [
                'success' => $this->isSuccessful,
                'error_message' => $this->isSuccessful ? null : $this->statusText,
                'data' => $this->isSuccessful ? $this->data : null,
            ];

            if (YII_ENV_DEV) {
                @file_put_contents(Yii::getAlias('@runtime/responses/')  . 'json_response_' . time() . '.json', json_encode($this->data, JSON_PRETTY_PRINT));
            }
        }

        $this->trigger(self::EVENT_BEFORE_SEND);
        $this->prepare();
        $this->trigger(self::EVENT_AFTER_PREPARE);
        $this->sendHeaders();
        $this->sendContent();
        $this->trigger(self::EVENT_AFTER_SEND);
        $this->isSent = true;
    }

}
