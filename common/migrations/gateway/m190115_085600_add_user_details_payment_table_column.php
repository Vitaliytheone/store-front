<?php

use yii\db\Migration;
use common\models\gateways\Sites;

/**
 * Class m190115_085600_add_user_details_payment_table_column
 */
class m190115_085600_add_user_details_payment_table_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $templateDb = Yii::$app->params['gatewayDefaultDatabase'];

        $this->execute("
            USE `" . $templateDb . "`;

            ALTER TABLE `payments` ADD `user_details` text NULL AFTER `status`;
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
        
                    ALTER TABLE `payments` ADD `user_details` text NULL AFTER `status`;
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

        $this->execute("
            USE `" . $templateDb . "`;

            ALTER TABLE `payments` DROP `user_details`;
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
        
                    ALTER TABLE `payments` DROP `user_details`;
                ");
            }

        }
    }
}
