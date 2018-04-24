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
                      `code` varchar(5) NOT NULL DEFAULT '' COMMENT 'Language code in IETF lang format',
                      `content` text COMMENT 'Json messages content',
                      `created_at` int(11) DEFAULT NULL,
                      `updated_at` int(11) DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `idx_lang_code` (`code`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
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
            } else {
                echo PHP_EOL . 'Database ' . $db . 'does not exist. Skipped!';
            }
        }

    }
}
