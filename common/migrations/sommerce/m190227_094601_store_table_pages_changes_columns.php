<?php

use yii\db\Migration;

/**
 * Class m190227_094601_store_table_pages_changes_columns
 */
class m190227_094601_store_table_pages_changes_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `store_template`;
            ALTER TABLE `pages` ADD `name` VARCHAR(300) NOT NULL AFTER `id`;
            ALTER TABLE `pages` CHANGE `title` `seo_title` varchar(300) NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `store_template`;
            ALTER TABLE `pages` DROP COLUMN `name`;
            ALTER TABLE `pages` CHANGE `seo_title` `title` varchar(300) NULL;
        ');
    }
}
