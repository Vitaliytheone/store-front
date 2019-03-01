<?php

use yii\db\Migration;

/**
 * Handles the creation of table `link_validations`.
 */
class m180412_115027_create_link_validations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `link_validations` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `link` varchar(1000) NOT NULL,
              `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - invalid; 1 - valid',
              `link_type` int(11) unsigned NOT NULL,
              `store_id` int(11) NOT NULL,
              `created_at` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `store_id` (`store_id`),
              CONSTRAINT `fk_link_validations__stores` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
            ) ENGINE=InnoDB;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('link_validations');
    }
}
