<?php

use yii\db\Migration;

/**
 * Handles adding in_progress to table `orders`.
 */
class m180602_065431_add_in_progress_column_to_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        foreach ((new \yii\db\Query())->select([
            'db_name',
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $db = $store['db_name'];

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            $this->execute("
                ALTER TABLE `{$db}`.`orders` ADD `in_progress` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 - disabled, 1 - enabled' AFTER `customer`;
            ");
        }


    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        foreach ((new \yii\db\Query())->select([
            'db_name',
        ])->from(DB_STORES . '.stores')->all() as $store) {
            $db = $store['db_name'];

            $isDbExist = Yii::$app->db
                ->createCommand("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'")
                ->queryScalar();

            if (!$isDbExist) {
                continue;
            }

            $this->execute("
                ALTER TABLE `{$db}`.`orders` DROP `in_progress`;
            ");
        }
    }
}
