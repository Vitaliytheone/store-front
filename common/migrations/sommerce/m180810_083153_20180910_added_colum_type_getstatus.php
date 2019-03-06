<?php

use yii\db\Migration;

/**
 * Class m180810_083153_20180910_added_colum_type_getstatus
 */
class m180810_083153_20180910_added_colum_type_getstatus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_PANELS . "`;
            ALTER TABLE `getstatus` ADD `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0 - panels external, 1 - panels internal, 2 - stores external, 3 - stores internal' AFTER `status`;
            ALTER TABLE `getstatus` ADD `updated_at` INT NOT NULL AFTER `type`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `stores` DROP `type`;
            ALTER TABLE `stores` DROP `updated_at`;
        ');

    }
}
