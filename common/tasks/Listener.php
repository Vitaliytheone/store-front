<?php
namespace common\tasks;

use common\tasks\workers\BaseWorker;
use Yii;
use common\tasks\workers\MailerWorker;
use common\tasks\workers\TestWorker;
use yii\base\InvalidCallException;

/**
 * Class Listener
 * @package app\tasks
 */
class Listener {

    /**
     * Run workers
     * @param string $code
     * @param mixed $data
     * @param mixed $response
     * @return null|mixed
     */
    public static function run($code, $data, &$response = null)
    {
        $return = null;

        /**
         * @var BaseWorker $model
         */
        switch ($code) {
            case 'test':
                $return = ($model = Yii::$container->get(TestWorker::class, [$data]))
                    ->run();
            break;

            case 'mail':
                $return = ($model = Yii::$container->get(MailerWorker::class, [$data]))
                    ->run();
            break;

            default:
                throw new InvalidCallException();
        }

        if (!$return) {
            $response = $model->getError();
        }

        return $return;
    }
}