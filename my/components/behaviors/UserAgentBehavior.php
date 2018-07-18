<?php
namespace my\components\behaviors;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class UserAgentBehavior extends AttributeBehavior {

    public $attributeName;

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
    public function init() {
        parent::init();
        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [
                    $this->attributeName
                ],
            ];
        }
    }
    /**
     * @inheritdoc
     */
    protected function getValue($event) {
        if (is_string($this->value)) {
            return $this->value;
        } else {
            if ($this->value !== null) {
                return call_user_func($this->value, $event);
            }

            if (!empty(Yii::$app->request->userAgent)) {
                return Yii::$app->request->userAgent;
            }

            return null;
        }
    }
}