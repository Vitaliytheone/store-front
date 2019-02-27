<?php

namespace sommerce\components\validators\product;

use common\models\store\Pages;
use common\models\store\Products;
use yii\validators\Validator;

/**
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
        if (empty($model->$attribute) || $model->hasErrors()) {
            return false;
        }


        $url = trim($model->$attribute, ' ');
        $url = trim($url, '_');
        $url = trim($url, '-');

        $url = !empty($url) ? $url : Products::NEW_PRODUCT_URL_PREFIX . $model->id;

        $_url = $url;
        $postfix = 1;

        while (Pages::findOne(['url' => $_url])) {
            $_url = $url . '-' . $postfix;
            $postfix++;
        };

        $model->$attribute = $_url;
        $model->save(false);

        return true;
    }
}