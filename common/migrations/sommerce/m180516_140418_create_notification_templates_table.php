<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification_templates`.
 */
class m180516_140418_create_notification_templates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $dbs = ['store_template'];
        foreach ((new \yii\db\Query())->select([
            'db_name'
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $dbs[] = $store['db_name'];
        }

        foreach ($dbs as $db) {
            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            $this->execute('
                USE `' . $db . '`;
                CREATE TABLE `notification_templates` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `notification_code` varchar(255) NOT NULL,
                  `status` tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'0 - disabled, 1 - enabled\',
                  `subject` varchar(255) DEFAULT NULL,
                  `body` text,
                  `created_at` int(11) NOT NULL,
                  `updated_at` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB;
            ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $dbs = ['store_template'];
        foreach ((new \yii\db\Query())->select([
            'db_name'
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $dbs[] = $store['db_name'];
        }

        foreach ($dbs as $db) {
            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            $this->execute('
                USE `' . $db . '`;
                DROP TABLE `notification_templates`;
            ');
        }
    }
}
