<?php
namespace control_panel\components\validators;

use Yii;
use common\models\sommerces\Orders;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class OrderLimitValidator
 * @package control_panel\components\validators
 */
class OrderLimitValidator extends Validator
{

    /**
     * Validate domain
     * @param Model $model
     * @param mixed $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        if ($model->hasErrors()) {
            return false;
        }

        $customerId = $model->getUser()->id ?? null;
        if (empty($customerId)) {
            return false;
        }

        if (!Orders::can('create_panel', [
            'customerId' => $customerId
        ])) {
            $model->addError($attribute, Yii::t('app', 'error.panel.orders_limit_exceeded'));
            return false;
        }

        return true;
    }
}