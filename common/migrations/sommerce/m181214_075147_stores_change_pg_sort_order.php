<?php

use yii\db\Migration;

/**
 * Class m181214_075147_stores_change_pg_sort_order
 */
class m181214_075147_stores_change_pg_sort_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('USE `' . DB_STORES . '`');
        $pg = Yii::$app->db->createCommand('SELECT * FROM `payment_gateways` ORDER BY `position`;')->queryAll();
        $pos = 0;

        foreach ($pg as $key => $item) {
            $id = $item['id'];
            $this->execute("UPDATE `payment_gateways` SET `position` = {$pos} WHERE `id` = {$id}");
            $pos++;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }


}
