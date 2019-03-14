<?php

namespace common\components\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class PaymentHashBehavior extends AttributeBehavior
{
    /**
     * @var callable|string
     * This can be either an anonymous function that returns the IP value or a string.
     * If not set, it will use the value of `\Yii::$app->request->userIp` to set the attributes.
     * NOTE! Null is returned if the user IP address cannot be detected.
     */
    public $value;

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        /** @var $modelName ActiveRecord */
        $modelName = get_class($this->owner);

        do {

            $hash = static::generateRandomString() . '-' . static::generateRandomString() . '-' . static::generateRandomString();

        } while ($modelName::findOne(['hash' => $hash]));

        return $hash;
    }

    /**
     * Random string generator
     * @param int $length
     * @return string
     */
    protected static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}