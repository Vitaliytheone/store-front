<?php

use yii\db\Migration;

class m180315_115048_store_packages_indexes extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m180315_113338_store_pages_indexes cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `store`;
            ALTER TABLE `packages` ADD INDEX `idx_product_id` (`product_id`);
        ');
    }

    public function down()
    {
        $this->execute('
            USE `store`;
            ALTER TABLE `packages` DROP INDEX `idx_product_id`;
        ');
    }
}
