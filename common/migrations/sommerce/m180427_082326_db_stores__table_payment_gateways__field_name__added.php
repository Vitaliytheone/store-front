<?php

use yii\db\Migration;

/**
 * Class m180427_082326_db_stores__table_payment_gateways__field_name__added
 */
class m180427_082326_db_stores__table_payment_gateways__field_name__added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180427_082326_db_stores__table_payment_gateways__field_name__added cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            ALTER TABLE `payment_gateways` ADD `name` VARCHAR(300)  NULL  DEFAULT NULL  AFTER `currencies`;
        ');

        $this->execute('
            USE `' . DB_STORES . '`;
            UPDATE `payment_gateways` SET `name` = \'PayPal\' WHERE `method` = \'paypal\';
            UPDATE `payment_gateways` SET `name` = \'2Checkout\' WHERE `method` = \'2checkout\';
            UPDATE `payment_gateways` SET `name` = \'CoinPayments\' WHERE `method` = \'coinpayments\';
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            ALTER TABLE `payment_gateways` DROP `name`;
        ');
    }
}
