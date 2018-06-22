<?php

use yii\db\Migration;

/**
 * Class m180622_083257_db_panels__table_ssl_cert_item__generator_field_added
 */
class m180622_083257_db_panels__table_ssl_cert_item__generator_field_added extends Migration
{
    public function up()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            ALTER TABLE `ssl_cert_item` ADD `generator` TINYINT(1)  NULL  DEFAULT NULL  AFTER `allow`;
        ");

        $this->execute("
            USE `" . DB_PANELS . "`;
            UPDATE `ssl_cert_item` SET `generator` = '1' WHERE `product_id` = '45';
            UPDATE `ssl_cert_item` SET `generator` = '1' WHERE `product_id` = '75';
            UPDATE `ssl_cert_item` SET `generator` = '2' WHERE `product_id` = '31';
        ");
    }

    public function down()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            ALTER TABLE `ssl_cert_item` DROP `generator`;
        ");
    }
}
