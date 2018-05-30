<?php

use yii\db\Migration;

/**
 * Handles adding code to table `orders`.
 */
class m180530_135349_add_code_column_to_orders_table extends Migration
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
                ALTER TABLE `{$db}`.`orders` ADD `code` varchar(64) NOT NULL AFTER `id`;
                ALTER TABLE `{$db}`.`orders` ADD INDEX `idx_code` (`code`);
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
                ALTER TABLE `{$db}`.`orders` DROP `code`;
            ");
        }
    }
}
