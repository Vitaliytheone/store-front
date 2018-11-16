<?php
namespace common\models\panels\services;

use yii\helpers\ArrayHelper;
use common\models\panels\PaymentMethodsCurrency;
use common\models\panels\Project;
use yii\db\Query;
use Yii;

/**
 * Class GetPaymentMethodsCurrencyService
 * @package common\models\panels\services
 */
class GetPaymentMethodsCurrencyService {

    /**
     * @var Project
     */
    private $_panel;

    /**
     * GetPaymentMethodsCurrencyService constructor.
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
        $query = (new Query())
            ->select([
                'id',
                'method_id',
                'currency',
                'exchange_currency',
                'hidden',
                'auto_exchange_rate',
                'settings_form',
                'settings_form_description',
                'position'
            ])
            ->from(['pm' => DB_PANELS . '.' . PaymentMethodsCurrency::tableName()])
            ->orderBy([
                'position' => SORT_ASC
            ]);

        $paymentMethods = [];

        foreach ($query->all() as $method) {
            $settingsForm = (array)(!empty($method['settings_form']) ? json_decode($method['settings_form'], true) : []);

            foreach ($settingsForm as &$value) {
                $label = Yii::t('admin/payment_method', $value['label'], [
                    'currency' => $this->_panel->getCurrencyCode()
                ], 'en');
                $value['label'] = $label == $value['label'] ? $value['code'] : $label;
            }

            ArrayHelper::setValue($paymentMethods, [$method['method_id'], $method['currency']], [
                'method_id' => $method['method_id'],
                'currency' => $method['currency'],
                'exchange_currency' => !empty($method['exchange_currency']) ? json_decode($method['exchange_currency'], true) : [],
                'hidden' => (int)$method['hidden'],
                'auto_exchange_rate' => (int)$method['auto_exchange_rate'],
                'position' => (int)$method['position'],
                'settings_form' => $settingsForm,
                'settings_form_description' => str_replace([
                    '{currency}',
                    '{site}',
                ], [
                    $this->_panel ? $method['currency'] : '',
                    $this->_panel ? $this->_panel->getSiteUrl() : '',
                ], (string)$method['settings_form_description']),
            ]);
        }

        return $paymentMethods;
    }
}