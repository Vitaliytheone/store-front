<?php
namespace my\components\validators;

use common\helpers\CurlHelper;
use my\helpers\DomainsHelper;
use common\models\panels\OrderLogs;
use Yii;
use common\models\panels\Orders;
use common\models\panels\Project;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class OrderLimitValidator
 * @package my\components\validators
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