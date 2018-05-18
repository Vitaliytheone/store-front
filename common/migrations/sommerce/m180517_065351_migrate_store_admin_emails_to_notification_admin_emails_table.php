<?php

use yii\db\Migration;

/**
 * Class m180517_065351_migrate_store_admin_emails_to_notification_admin_emails_table
 */
class m180517_065351_migrate_store_admin_emails_to_notification_admin_emails_table extends Migration
{
    public function up()
    {
        foreach ((new \yii\db\Query())->select([
            'db_name',
            'admin_email'
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $db = $store['db_name'];
            $adminEmail = $store['admin_email'];

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            $this->execute("INSERT INTO `{$db}`.`notification_admin_emails` (`email`, `status`, `primary`) VALUES ('{$adminEmail}', 1, 1)");
        }
    }

    public function down()
    {
        foreach ((new \yii\db\Query())->select([
            'db_name'
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $db = $store['db_name'];

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            $adminEmail = (new \yii\db\Query())->select([
                'email'
            ])->from($db . '.notification_admin_emails')->andWhere('`primary` = 1')->scalar();

            if ($adminEmail) {
                $this->execute("UPDATE " . DB_STORES . ".`stores` SET `admin_email` = '{$adminEmail}' WHERE `db_name` = '{$db}';");
            }

            $this->execute("DELETE FROM " . $db . ".notification_admin_emails WHERE `primary` = 1");
        }
    }
}
