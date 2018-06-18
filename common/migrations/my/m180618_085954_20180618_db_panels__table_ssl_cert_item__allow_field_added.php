<?php

use yii\db\Migration;

/**
 * Class m180618_085954_20180618_db_panels__table_ssl_cert_item__allow_field_added
 */
class m180618_085954_20180618_db_panels__table_ssl_cert_item__allow_field_added extends Migration
{
    public function up()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            ALTER TABLE `ssl_cert_item` ADD `allow` TEXT  NULL  COMMENT 'List of ids of allowed users for this cert. Allowed for all if NULL'  AFTER `price`;
        ");
    }

    public function down()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            DROP TABLE `ssl_cert_item`;
        ");
    }
}
