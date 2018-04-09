<?php

use yii\db\Migration;

/**
 * Class m180409_063605_db_stores_table_payment_gateways
 */
class m180409_063605_db_stores_table_payment_gateways extends Migration
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
        echo "m180409_063605_db_stores_table_payment_gateways cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute('
            USE `stores`;
    
            DROP TABLE IF EXISTS `payment_gateways`;
    
            CREATE TABLE `payment_gateways` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `method` varchar(255) NOT NULL DEFAULT \'\',
              `currencies` varchar(3000) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            LOCK TABLES `payment_gateways` WRITE;
            /*!40000 ALTER TABLE `payment_gateways` DISABLE KEYS */;
            
            INSERT INTO `payment_gateways` (`id`, `method`, `currencies`)
            VALUES
              (1,\'paypal\',\'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\"]\'),
              (2,\'2checkout\',\'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\"]\'),
              (3,\'coinpayments\',\'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\"]\');
            
            /*!40000 ALTER TABLE `payment_gateways` ENABLE KEYS */;
            UNLOCK TABLES;
        ');
    }

    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `payment_gateways`');
    }
}
