<?php
namespace common\models\panels\services;

use common\models\panels\Params;
use yii\helpers\ArrayHelper;

/**
 * Class GetPaymentMethodsService
 * @package common\models\panels\services
 */
class GetPaymentMethodsService
{
    /**
     * @var int|null
     */
    private $visibility;

    /**
     * Owner constructor.
     * @param integer|null $visibility
     */
    public function __construct($visibility = null)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return array
     */
    public function get()
    {
        $methods = [];
        foreach (ArrayHelper::getValue(Params::getAll(), Params::CATEGORY_PAYMENT, []) as $code => $method) {
            $options = (array)ArrayHelper::getValue($method, 'options', []);
            $visibility = (int)ArrayHelper::getValue($options, 'visibility', 1);

            if (null !== $this->visibility) {
                if ($this->visibility != $visibility) {
                    continue;
                }
            }

            $methods[$code] = array_merge([
                'code' => $code,
            ], $options);
        }

        return $methods;
    }
}