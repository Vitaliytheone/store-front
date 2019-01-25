<?php
namespace common\models\panels\services;

use yii\helpers\ArrayHelper;
use common\models\panels\Project;
use Yii;

/**
 * Class GetAvailablePaymentMethodsService
 * @package common\models\panels\services
 */
class GetAvailablePaymentMethodsService {

    /**
     * @var Project
     */
    private $_panel;

    /**
     * GetPaymentMethodsService constructor.
     * @param Project $panel
     */
    public function __construct(Project $panel)
    {
        $this->_panel = $panel;
    }

    /**
     * @return array
     */
    public function get()
    {
        $paymentMethodsCurrency = Yii::$container->get(GetPaymentMethodsCurrencyService::class, [$this->_panel])->get();
        $paymentMethods = Yii::$container->get(GetPaymentMethodsService::class, [$this->_panel])->get();

        foreach ($paymentMethods as $key => &$method) {
            $currencyPaymentMethod = ArrayHelper::getValue($paymentMethodsCurrency, [$method['id'], $this->_panel->getCurrencyCode()]);

            // Skip method without method configs
            if (empty($currencyPaymentMethod)) {
                unset($paymentMethods[$key]);
                continue;
            }

            $description = trim((string)$currencyPaymentMethod['settings_form_description']);
            $description = !empty($description) ? $description : $method['settings_form_description'];

            $method['currency'] = $currencyPaymentMethod['currency'];
            $method['settings_form_description'] = $description;
            $method['exchange_currency'] = $currencyPaymentMethod['exchange_currency'];
            $method['hidden'] = (int)$currencyPaymentMethod['hidden'];
            $method['auto_exchange_rate'] = (int)$currencyPaymentMethod['auto_exchange_rate'];
            $method['position'] = (int)$currencyPaymentMethod['position'];
        }

        ArrayHelper::multisort($paymentMethods, 'position');

        return ArrayHelper::index($paymentMethods, 'id');
    }
}