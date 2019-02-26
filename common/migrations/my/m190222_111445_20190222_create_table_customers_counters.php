<?php

use yii\db\Migration;

/**
 * Class m190222_111445_20190222_create_table_customers_counters
 */
class m190222_111445_20190222_create_table_customers_counters extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE " . DB_PANELS . ";

            CREATE TABLE customers_counters (
              id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              customer_id int NOT NULL UNIQUE,
              stores int NOT NULL DEFAULT 0,
              panels int NOT NULL DEFAULT 0,
              child_panels int NOT NULL DEFAULT 0,
              gateways int NOT NULL DEFAULT 0,
              domains int NOT NULL DEFAULT 0,
              ssl_certs int NOT NULL DEFAULT 0,
              created_at int,
              updated_at int,
              CONSTRAINT fk_customers_counters_customers FOREIGN KEY (customer_id)
                REFERENCES customers(id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('customers_counters');
    }
}
