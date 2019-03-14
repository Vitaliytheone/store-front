<?php

namespace sommerce\components\validators\product;

use common\models\sommerce\Pages;
use common\models\sommerce\Products;
use common\models\sommerces\PaymentMethods;
use Yii;
use yii\validators\Validator;

/**
 * Custom link prepare and validate with rules
 *
 * Class UrlValidator
 * @package sommerce\components\validators\product
 */
class UrlValidator extends Validator
{
    /**
     * Validate attribute
     * @param \yii\base\Model $model
     * @param string $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        /** @var Products|Pages $model */
        if (empty($attribute) || empty($model->$attribute) || $model->hasErrors()) {
            return false;
        }

        $url = mb_strtolower($model->$attribute);
        $url = trim($url, ' -_');

        if (preg_match('/^[a-z\d][a-z\d-]*[a-z\d]$/u', $url) !== 1) {
            $this->addError($model, $attribute, Yii::t('admin', 'pages.link_invalid'));
            return false;
        }
        $url = preg_replace('/(-)\\1+/', '-', $url);

        if (Pages::findOne(['url' => $url]) || PaymentMethods::findOne(['url' => $url])) {
            $this->addError($model, $attribute, Yii::t('admin', 'pages.link_exist'));
            return false;
        }

        $model->$attribute = $url;

        return true;
    }
}