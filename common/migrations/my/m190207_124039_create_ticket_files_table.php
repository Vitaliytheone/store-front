<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ticket_files`.
 */
class m190207_124039_create_ticket_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('

            CREATE TABLE `ticket_files` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `customer_id` int(11) NOT NULL,
              `ticket_id` int(11) NOT NULL,
              `admin_id` int(11) NOT NULL,
              `message_id` int(11) NOT NULL,
              `link` varchar(255) NOT NULL,
              `cdn_id` varchar(255) NOT NULL,
              `mime` varchar(255) NOT NULL,
              `details` varchar(10000) NOT NULL,
              `created_at` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            ALTER TABLE `ticket_files` ADD CONSTRAINT `fk_ticket_files_tickets` FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
            ALTER TABLE `ticket_files` ADD CONSTRAINT `fk_ticket_files_ticket_message` FOREIGN KEY (`message_id`) REFERENCES `ticket_messages`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
             USE `' . DB_PANELS . '`;
             DROP TABLE `ticket_files`;
        ');
    }
}
