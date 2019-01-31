<?php

use yii\db\Migration;

/**
 * Class m190129_082541_gateway_update_themes_files_table
 */
class m190129_082541_gateway_update_themes_files_table extends Migration
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
                
            ALTER TABLE `themes_files`
            DROP `theme_id`,
            ADD `url` varchar(300) NULL AFTER `name`,
            ADD `file_type` varchar(300) NULL AFTER `url`,
            ADD `mime` varchar(300) NULL AFTER `file_type`,
            ADD `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not default; 1 - default' AFTER `content`,
            ADD `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not deleted; 1 - deleted' AFTER `is_default`,
            RENAME TO `files`;
            
            ALTER TABLE `files`
            CHANGE `content` `content` blob NOT NULL AFTER `mime`;
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
            
            ALTER TABLE `files`
            ADD `theme_id` int(11) NOT NULL AFTER `id`,
            DROP `url`,
            DROP `file_type`,
            DROP `mime`,
            DROP `is_default`,
            DROP `is_deleted`,
            RENAME TO `themes_files`;
            
            ALTER TABLE `files`
            CHANGE `content` `content` text NOT NULL AFTER `mime`;
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
