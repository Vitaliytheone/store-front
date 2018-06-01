<?php

use yii\db\Migration;
use common\models\store\Orders;

/**
 * Handles adding code to table `orders`.
 */
class m180530_140603_add_code_to_old_orders extends Migration
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

            foreach ((new \yii\db\Query())->select([
                'id',
            ])->from($db . '.orders')->all() as $order) {
                $this->execute("UPDATE `{$db}`.`orders` SET `code` = '" . Orders::generateCodeString(). "' WHERE `id` = '" . $order['id'] . "';");
            }
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
                UPDATE `{$db}`.`orders` SET `code` = NULL;
            ");
        }
    }
}
