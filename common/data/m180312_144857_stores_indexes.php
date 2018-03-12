<?php

use yii\db\Migration;

class m180312_144857_stores_indexes extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m180312_144857_stores_indexes cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute("
            USE `stores`;
            ALTER TABLE `store_admins_hash` ADD INDEX `idx_hash` (`hash`);
            ALTER TABLE `store_admins_hash` ADD INDEX `idx_admin_id` (`admin_id`);
            ALTER TABLE `store_admins` ADD INDEX `idx_id_store_id_status` (`id`, `store_id`, `status`);
            ALTER TABLE `store_domains` ADD INDEX `idx_store_id_type` (`store_id`, `type`);
        ");
    }

    public function down()
    {
        $this->execute("
            USE `stores`;
            ALTER TABLE `store_admins_hash` DROP INDEX `idx_hash`;
            ALTER TABLE `store_admins_hash` DROP INDEX `idx_admin_id`;
            ALTER TABLE `store_admins` DROP INDEX `idx_id_store_id_status`;
            ALTER TABLE `store_domains` DROP INDEX `idx_store_id_type`;
        ");
    }
}
