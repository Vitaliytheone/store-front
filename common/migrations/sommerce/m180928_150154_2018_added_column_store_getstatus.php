<?php

use yii\db\Migration;

/**
 * Class m180928_150154_2018_added_column_store_getstatus
 */
class m180928_150154_2018_added_column_store_getstatus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
           ALTER TABLE `getstatus` ADD `store` tinyint(1) DEFAULT 0 AFTER `type`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `stores` DROP `store`;
        ');

    }

}



