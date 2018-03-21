<?php

use yii\db\Migration;

class m180315_083310_stores_drop_table_store_files extends Migration
{
    public function safeUp()
    {

    }

    public function safeDown()
    {
        echo "m180315_083310_stores_drop_table_store_files cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `stores`;
            DROP TABLE `stores`.`store_files`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `stores`;
            CREATE TABLE IF NOT EXISTS `stores`.`store_files` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `store_id` INT NULL,
              `type` TINYINT(1) NULL COMMENT \'1 - logo, 2 - favicon\',
              `date` TEXT(65535) NULL,
              `created_at` INT NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_files_store_id_idx` (`store_id` ASC),
              CONSTRAINT `fk_files_store_id`
                FOREIGN KEY (`store_id`)
                REFERENCES `stores`.`stores` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;
        ');
    }
}
