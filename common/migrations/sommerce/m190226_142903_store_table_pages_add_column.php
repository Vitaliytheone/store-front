<?php

use yii\db\Migration;

/**
 * Class m190226_142903_store_table_pages_add_column
 */
class m190226_142903_store_table_pages_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `store_template`;
            ALTER TABLE `pages` ADD `seo_description` VARCHAR(2000) NULL DEFAULT NULL AFTER `title`;
            ALTER TABLE `pages` ADD `seo_keywords` VARCHAR(2000) NULL DEFAULT NULL AFTER `seo_description`;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `store_template`;
            ALTER TABLE `pages` DROP COLUMN `seo_description`;
            ALTER TABLE `pages` DROP COLUMN `seo_keywords`;
        ');
    }


}
