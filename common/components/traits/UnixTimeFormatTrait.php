<?php

namespace common\components\traits;

use Yii;

/**
 * Class UnixTimeFormatTrait
 * @package common\components\traits
 */
trait UnixTimeFormatTrait {

    /**
     * Get formatted date time
     * @param string $attribute
     * @param string $format
     * @param integer $timezone
     * @return null|string
     */
    public function getFormattedDate($attribute, $format = 'php:Y-m-d H:i:s', $timezone = null)
    {
        if (empty($this->{$attribute})) {
            return null;
        }

        return static::formatDate($this->{$attribute}, $format, $timezone);
    }

    /**
     * Format date
     * @param $value
     * @param string $format
     * @param null $timezone
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public static function formatDate($value, $format = 'php:Y-m-d H:i:s', $timezone = null)
    {
        if (!empty(Yii::$app->controller)) {

            if (method_exists(Yii::$app->controller, 'getUser')) {
                $user = Yii::$app->controller->getUser();
            } else {
                $user = Yii::$app->user;
            }

            if ($user) {
                if (null === $timezone && !$user->isGuest) {
                    $user = $user->identity;
                    $timezone = !empty($user->timezone) ? $user->timezone : null;
                }
            }
        }

        $time = $value + ((int)$timezone) + Yii::$app->params['time'];

        return Yii::$app->formatter->asDate($time, $format);
    }
}