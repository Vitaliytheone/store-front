<?php

use yii\db\Migration;

/**
 * Handles the creation of table `super_admin_token`.
 */
class m180322_121909_create_super_admin_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `super_admin_token` (
              `id` int(11) NOT NULL,
              `super_admin_id` int(11) NOT NULL,
              `item_id` int(11) NOT NULL,
              `item` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - panels, 1 - my, 2 - sommerce admin',
              `token` varchar(64) NOT NULL,
              `expiry_at` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            ALTER TABLE `super_admin_token`
              ADD PRIMARY KEY (`id`);
            
            
            ALTER TABLE `super_admin_token`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            COMMIT;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('super_admin_token');
    }
}
