<?php

use yii\db\Migration;

/**
 * Class m190129_081231_gateways_remove_default_themes_table
 */
class m190129_081231_gateways_remove_default_themes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_GATEWAYS . "`;

            SET foreign_key_checks = 0;

            DROP TABLE IF EXISTS `default_themes`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            USE `" . DB_GATEWAYS . "`;

            SET foreign_key_checks = 0;

            DROP TABLE IF EXISTS `default_themes`;
            CREATE TABLE `default_themes` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(300) NOT NULL,
              `folder` varchar(300) NOT NULL,
              `position` int(11) NOT NULL,
              `thumbnail` varchar(300) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
            INSERT INTO `default_themes` (`id`, `name`, `folder`, `position`, `thumbnail`) VALUES
                (1, 'Default', 'default', 0, '/img/themes/preview_classic.png');
        ");
    }
}
