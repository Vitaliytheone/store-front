<?php

use yii\db\Migration;

/**
 * Class m180918_074232_stores_update_payment_gateway_class_names
 */
class m180918_074232_stores_update_payment_gateway_class_names extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute("
            USE `" . DB_STORES . "`;
            UPDATE payment_gateways SET class_name = CONCAT(UCASE(LEFT(class_name, 1)), SUBSTRING(class_name, 2));
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
