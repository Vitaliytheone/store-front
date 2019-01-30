<?php

use yii\db\Migration;

/**
 * Class m190129_081548_gateway_remove_pages_table
 */
class m190129_081548_gateway_remove_pages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $templateDb = Yii::$app->params['gatewayDefaultDatabase'];

        $query = "
            SET foreign_key_checks = 0;
            SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
                
            DROP TABLE IF EXISTS `pages`;
        ";

        $this->execute("
            USE `" . $templateDb . "`;

            $query;
        ");

        $sitesQuery = (new \yii\db\Query())
            ->select(['db_name'])
            ->from(DB_GATEWAYS . '.sites')
            ->andWhere("db_name IS NOT NULL AND db_name <> ''");

        foreach ($sitesQuery->batch() as $sites) {
            foreach ($sites as $site) {
                $dbName = $site['db_name'];

                $dbExist = Yii::$app->db
                    ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $dbName . "'")
                    ->queryScalar();

                if (!$dbExist) {
                    continue;
                }

                $this->execute("
                    USE `" . $dbName . "`;
        
                    $query
                ");
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $templateDb = Yii::$app->params['gatewayDefaultDatabase'];
        $query = "
            SET foreign_key_checks = 0;
            SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
            
            DROP TABLE IF EXISTS `pages`;
            CREATE TABLE `pages` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) DEFAULT NULL,
              `visibility` tinyint(1) DEFAULT NULL,
              `content` mediumtext,
              `seo_title` varchar(255) DEFAULT NULL,
              `seo_description` varchar(2000) DEFAULT NULL,
              `seo_keywords` varchar(2000) DEFAULT NULL,
              `url` varchar(255) DEFAULT NULL,
              `template_name` varchar(200) DEFAULT NULL,
              `deleted` tinyint(1) NOT NULL DEFAULT '0',
              `is_default` tinyint(1) NOT NULL DEFAULT '0',
              `created_at` int(11) DEFAULT NULL,
              `updated_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_deleted_visibility` (`deleted`,`visibility`),
              KEY `idx_deleted` (`deleted`)
            ) ENGINE=InnoDB;
        ";

        $this->execute("
            USE `" . $templateDb . "`;

            $query
        ");

        $sitesQuery = (new \yii\db\Query())
            ->select(['db_name'])
            ->from(DB_GATEWAYS . '.sites')
            ->andWhere("db_name IS NOT NULL AND db_name <> ''");

        foreach ($sitesQuery->batch() as $sites) {
            foreach ($sites as $site) {
                $dbName = $site['db_name'];

                $dbExist = Yii::$app->db
                    ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $dbName . "'")
                    ->queryScalar();

                if (!$dbExist) {
                    continue;
                }

                $this->execute("
                    USE `" . $dbName . "`;
        
                    $query
                ");
            }

        }
    }

}
