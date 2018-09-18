<?php

use yii\db\Migration;

/**
 * Class m180918_132207_20180818_stores_add_mercadopago_payment_gateways
 */
class m180918_132207_20180818_stores_add_mercadopago_payment_gateways extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(DB_STORES . 'payment_gateways', [
            'method' => 'mercadopago',
            'currencies' => '["BRL"]',
            'name' => 'MercadoPago',
            'class_name' => 'Mercadopago',
            'url' => 'mercadopago',
            'options' => '{"client_id":"","secret":""}',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(DB_STORES . 'payment_gateways', ['method' => 'mercadopago']);
    }
}
