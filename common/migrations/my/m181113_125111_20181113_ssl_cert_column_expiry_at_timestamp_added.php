<?php

use yii\db\Migration;

/**
 * Class m181113_125111_20181113_ssl_cert_column_expiry_at_timestamp_added
 */
class m181113_125111_20181113_ssl_cert_column_expiry_at_timestamp_added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `ssl_cert` ADD `expiry_at_timestamp` INT(11)  UNSIGNED  NULL  DEFAULT NULL  COMMENT \'Expiry date in timestamp format\'  AFTER `expiry`;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE `ssl_cert` DROP `expiry`;');
    }

}
