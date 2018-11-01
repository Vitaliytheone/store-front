<?php

use yii\db\Migration;

/**
 * Class m181101_112752_20181101_table_letsencrypt_ssl
 */
class m181101_112752_20181101_table_letsencrypt_ssl extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE `letsencrypt_ssl` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `domain` varchar(300) DEFAULT NULL,
              `file_contents` text,
              `expired_at` int(11) DEFAULT NULL,
              `updated_at` int(11) DEFAULT NULL,
              `created_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ');

        $this->execute('ALTER TABLE `letsencrypt_ssl` ADD UNIQUE INDEX (`domain`);');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
       $this->execute('DROP TABLE `letsencrypt_ssl`');
    }
}
