<?php

use yii\db\Migration;

/**
 * Class m181102_115928_20181102_ssl_cert_item__field__provider__added
 */
class m181102_115928_20181102_ssl_cert_item__field__provider__added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `ssl_cert_item` ADD `provider` TINYINT(1)  NULL  DEFAULT NULL  COMMENT \'1 - gogetssl, 2 - letsencrypt\'  AFTER `generator`;');
        $this->execute('UPDATE `ssl_cert_item` SET `provider` = \'1\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE `ssl_cert_item` DROP `provider`;');
    }
}
