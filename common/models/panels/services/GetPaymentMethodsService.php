<?php
namespace common\models\panels\services;

use common\models\panels\PaymentMethods;
use yii\db\Query;

/**
 * Class GetPaymentMethodsService
 * @package common\models\main\services
 */
class GetPaymentMethodsService {

    /**
     * @return array
     */
    public function get()
    {
        $query = (new Query())
            ->select([
                'id',
                'currency',
                'method_name',
                'class_name',
                'url',
                'addfunds_form',
                'settings_form',
                'settings_form_description',
                'multi_currency',
                'hidden',
                'auto_exchange_rate',
                'manual_callback_url',
            ])
            ->from(['pm' => DB_PANELS . '.' . PaymentMethods::tableName()])
            ->orderBy([
                'position' => SORT_ASC
            ]);

        $paymentMethods = [];

        foreach ($query->all() as $method) {
            $paymentMethods[$method['id']] = [
                'id' => $method['id'],
                'currency' => !empty($method['currency']) ? json_decode($method['currency'], true) : [],
                'method_name' => $method['method_name'],
                'class_name' => $method['class_name'],
                'url' => $method['url'],
                'addfunds_form' => !empty($method['addfunds_form']) ? json_decode($method['addfunds_form'], true) : [],
                'settings_form' => !empty($method['settings_form']) ? json_decode($method['settings_form'], true) : [],
                'settings_form_description' => $method['settings_form_description'],
                'multi_currency' => !empty($method['multi_currency']) ? json_decode($method['multi_currency'], true) : [],
                'hidden' => (int)$method['hidden'],
                'auto_exchange_rate' => (int)$method['auto_exchange_rate'],
                'manual_callback_url' => (int)$method['manual_callback_url'],
            ];
        }

        return $paymentMethods;
    }
}