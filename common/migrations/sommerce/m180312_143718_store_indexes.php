<?php

use yii\db\Migration;

class m180312_143718_store_indexes extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m180312_143718_store_indexes cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute("
            USE `store`;
            ALTER TABLE `navigation` ADD INDEX `idx_parent_id_position` (`parent_id`, `position`);
            ALTER TABLE `carts` ADD INDEX `idx_key` (`key`);
            ALTER TABLE `suborders` ADD INDEX `idx_order_id_mode` (`order_id`, `mode`);
            ALTER TABLE `suborders` ADD INDEX `idx_mode` (`mode`);
            ALTER TABLE `payments` ADD INDEX `idx_status` (`status`);
        ");
    }

    public function down()
    {
        $this->execute("
            USE `store`;
            ALTER TABLE `navigation` DROP INDEX `idx_parent_id_position`;
            ALTER TABLE `carts` DROP INDEX `idx_key`;
            ALTER TABLE `suborders` DROP INDEX `idx_order_id_mode`;
            ALTER TABLE `suborders` DROP INDEX `idx_mode`;
            ALTER TABLE `payments` DROP INDEX `idx_status`;
        ");
    }
}
