<?php

use yii\db\Migration;

/**
 * Handles the creation of table `payment_methods_currency`.
 */
class m190116_083633_create_payment_methods_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            CREATE TABLE `payment_methods_currency` (
              id int(11) unsigned NOT NULL,
              method_id int(11) unsigned,
              currency char(3),
              position int(11),
              settings_form text DEFAULT NULL,
              settings_form_description text DEFAULT NULL,
              hidden smallint(1) DEFAULT 0,
              created_at int(11),
              updated_at int(11)
            );
            
            ALTER TABLE `payment_methods_currency`
              ADD PRIMARY KEY (id);
            
            ALTER TABLE `payment_methods_currency`
            CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
            
            CREATE INDEX idx_method_id
            ON `payment_methods_currency` (method_id);
            
            ALTER TABLE `payment_methods_currency`
              ADD CONSTRAINT `fk_payment_methods_currency_payment_methods` FOREIGN KEY (`method_id`) REFERENCES `payment_methods`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('USE `' . DB_STORES . '`;
        
        DROP TABLE `payment_methods_currency`;');
    }
}
