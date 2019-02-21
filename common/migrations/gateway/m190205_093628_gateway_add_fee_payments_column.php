<?php

use yii\db\Migration;

/**
 * Class m190205_093628_gateway_add_fee_payments_column
 */
class m190205_093628_gateway_add_fee_payments_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $templateDb = Yii::$app->params['gatewayDefaultDatabase'];

        $query = "
            ALTER TABLE `payments` ADD `take_fee_from_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not active; 1 - active' AFTER `transaction_id`;
            ALTER TABLE `payments` ADD `fee` decimal(20,5) NULL AFTER `take_fee_from_user`;
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
            ALTER TABLE `payments` DROP `take_fee_from_user`;
            ALTER TABLE `payments` DROP `fee`;
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
