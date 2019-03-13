<?php

namespace sommerce\components\validators\product;

use common\models\sommerce\Pages;
use common\models\sommerce\Products;
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
        /**
         * @var Products $model
         */
        if (empty($attribute) || $model->hasErrors()) {
            return false;
        }

        $url = trim($model->$attribute, ' -_');
        $url = preg_replace('/([^a-zA-Z\d\s_-])+/iu', '', $url);
        $url = preg_replace('/([\s_])+/', '-', $url);
        $url = preg_replace('/(-)\\1+/', '-', $url);
        $url = strtolower($url);

        if (preg_match('/^[a-z\d][a-z\d-]*[a-z\d]$/iu', $url) !== 1) {
            $this->addError($model, $attribute, Yii::t('admin', 'pages.link_invalid'));
            return false;
        }

        $url = !empty($url) ? $url : Products::NEW_PRODUCT_URL_PREFIX . $model->id;


        if (Pages::findOne(['url' => $url])) {
            $this->addError($model, $attribute, Yii::t('admin', 'pages.link_exist'));
            return false;
        }

        $model->$attribute = $url;

        return true;
    }
}