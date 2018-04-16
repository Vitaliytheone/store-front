<?php

use yii\db\Migration;

/**
 * Class m180416_114504_buy_domains_for_all_customers_on
 */
class m180416_114504_buy_domains_for_all_customers_on extends Migration
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
        echo "m180416_114504_buy_domains_for_all_customers_on cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute('
            USE `panels`;
            UPDATE `customers` SET `buy_domain` = \'1\' WHERE `buy_domain` = \'0\';
        ');
    }

    public function down()
    {
        echo "m180416_114504_buy_domains_for_all_customers_on cannot be reverted.\n";

        return false;
    }
}
