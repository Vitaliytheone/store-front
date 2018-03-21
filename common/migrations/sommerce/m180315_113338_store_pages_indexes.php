<?php

use yii\db\Migration;

class m180315_113338_store_pages_indexes extends Migration
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
            ALTER TABLE `pages` ADD INDEX `idx_deleted_visibility` (`deleted`, `visibility`);
            ALTER TABLE `pages` ADD INDEX `idx_deleted` (`deleted`);
        ');
    }

    public function down()
    {
        $this->execute('
            USE `store`;
            ALTER TABLE `pages` DROP INDEX `idx_deleted_visibility`;
            ALTER TABLE `pages` DROP INDEX `idx_deleted`;
        ');
    }
}
