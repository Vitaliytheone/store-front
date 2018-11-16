<?php
namespace common\models\panels\services;

use common\helpers\CurrencyHelper;
use common\models\panels\Project;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class GetPanelAvailablePaymentMethodsService
 * @package common\models\panels\services
 */
class GetPanelAvailablePaymentMethodsService {

    /**
     * @var Project
     */
    private $_panel;


    /**
     * GetPanelAvailablePaymentMethodsService constructor.
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
        $options = [];
        $methods = Yii::$container->get(GetPaymentMethodsService::class, [$this->_panel])->get();
        $panelPaymentMethods = Yii::$container->get(GetPanelPaymentMethodsService::class, [$this->_panel])->get();
        $paymentMethodsCurrency = Yii::$container->get(GetPaymentMethodsCurrencyService::class, [$this->_panel])->get();
        $panelPaymentMethods = ArrayHelper::index($panelPaymentMethods, 'currency_id');

        foreach (CurrencyHelper::getAvailableCurrencies() as $currency) {
            $currencyMethods = ArrayHelper::getValue($paymentMethodsCurrency, $currency, []);
            foreach ($currencyMethods as $methodId => $currencyMethod) {
                if (!empty($panelPaymentMethods[$currencyMethod['currency_id']])) {
                    continue;
                }

                $method = $methods[$methodId];
                $group = Yii::t('app/superadmin', 'panels.edit.payment_method_modal.select.available_for', [
                    'currency' => $currency
                ]);

                $options[$group][$currencyMethod['id']] = $method['method_name'];

            }
        }

        return $options;
    }
}