<?php

namespace common\models\sommerces\services;

use common\models\sommerces\PaymentMethods;
use common\models\sommerces\Stores;
use yii\db\Query;

/**
 * Class GetPaymentMethodsService
 * @package common\models\panels\services
 */
class GetPaymentMethodsService
{
    /**
     * @var Stores
     */
    private $_panel;

    /**
     * GetPaymentMethodsService constructor.
     * @param Stores $panel
     */
    public function __construct(Stores $panel)
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
                'method_name',
                'name',
                'class_name',
                'url',
                'addfunds_form',
                'settings_form',
                'settings_form_description',
                'manual_callback_url',
            ])
            ->from(['pm' => DB_SOMMERCES . '.' . PaymentMethods::tableName()]);

        $paymentMethods = [];

        foreach ($query->all() as $method) {
            $settingsForm = (array)(!empty($method['settings_form']) ? json_decode($method['settings_form'], true) : []);

            $description = $method['settings_form_description'];
            $description = str_replace([
                '{currency}',
                '{site}',
            ], [
                $this->_panel ? $this->_panel->currency : '',
                $this->_panel ? $this->_panel->domain : '',
            ], $description);

            $paymentMethods[$method['id']] = [
                'id' => $method['id'],
                'method_name' => $method['method_name'],
                'name' => $method['name'],
                'class_name' => $method['class_name'],
                'url' => $method['url'],
                'addfunds_form' => !empty($method['addfunds_form']) ? json_decode($method['addfunds_form'], true) : [],
                'settings_form' => $settingsForm,
                'settings_form_description' => $description,
                'manual_callback_url' => (int)$method['manual_callback_url'],
            ];
        }

        return $paymentMethods;
    }
}