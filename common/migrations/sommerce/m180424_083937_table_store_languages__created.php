<?php

use yii\db\Migration;
use common\models\stores\Stores;

/**
 * Class m180424_083937_table_store_languages__created
 */
class m180424_083937_table_store_languages__created extends Migration
{
    protected $storesDbs = [];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $storePrefix = trim(Stores::STORE_DB_NAME_PREFIX, '_');

        $this->storesDbs = Yii::$app->db
            ->createCommand("SELECT SCHEMA_NAME FROM `INFORMATION_SCHEMA`.`SCHEMATA` WHERE `SCHEMA_NAME` LIKE '$storePrefix\_%'")
            ->queryColumn();
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        foreach ($this->storesDbs as $db) {

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if ($isDbExist) {
                $this->execute("
                    USE $db;
                    CREATE TABLE `languages` (
                      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                      `code` varchar(5) DEFAULT NULL COMMENT 'Language code in IETF lang format',
                      `created_at` int(11) DEFAULT NULL,
                      `updated_at` int(11) DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `idx_lang_code` (`code`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
                $this->execute("
                    USE $db;
                    CREATE TABLE `messages` (
                      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                      `lang_code` varchar(10) DEFAULT NULL COMMENT 'Language code in IETF lang format',
                      `section` varchar(100) DEFAULT NULL COMMENT 'Message section',
                      `name` varchar(500) DEFAULT NULL COMMENT 'Message variable name',
                      `value` varchar(2000) DEFAULT NULL COMMENT 'Message text',
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    
                    ALTER TABLE `messages` ADD INDEX `idx_lang_code__name` (`lang_code`, `name`);
                ");
            } else {
                echo PHP_EOL . 'Database ' . $db . 'does not exist. Skipped!';
            }
        }
    }

    public function down()
    {
        foreach ($this->storesDbs as $db) {

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if ($isDbExist) {
                $this->execute("
                    USE $db;
                    DROP TABLE `languages`;
                ");
                $this->execute("
                    USE $db;
                    DROP TABLE `messages`;
                ");
            } else {
                echo PHP_EOL . 'Database ' . $db . 'does not exist. Skipped!';
            }
        }

    }
}
