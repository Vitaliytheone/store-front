<?php

use yii\db\Migration;

/**
 * Class m190201_083011_gateway_add_files_default_data
 */
class m190201_083011_gateway_add_files_default_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $templateDb = Yii::$app->params['gatewayDefaultDatabase'];

        $query = file_get_contents((dirname(__FILE__) . '/defaults/20180201_files_defaults.sql'));

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

    }
}
