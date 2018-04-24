<?php

use yii\db\Migration;

/**
 * Class m180424_125052_table_stores__default_languages_created
 */
class m180424_125052_table_stores__default_languages_created extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            CREATE TABLE `store_default_messages` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `lang_code` varchar(10) DEFAULT NULL COMMENT \'Language code in IETF lang format\',
              `section` varchar(100) DEFAULT NULL COMMENT \'Message section\',
              `name` varchar(500) DEFAULT NULL COMMENT \'Message variable name\',
              `value` varchar(2000) DEFAULT NULL COMMENT \'Message text\',
              PRIMARY KEY (`id`),
              KEY `idx__lang_code_name` (`lang_code`,`name`(191))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            ALTER TABLE `store_default_messages` ADD INDEX `idx_lang_code__name` (`lang_code`, `name`);
        ');
    }

    public function down()
    {
        $this->execute('DROP TABLE `store_default_messages`');
    }
}
