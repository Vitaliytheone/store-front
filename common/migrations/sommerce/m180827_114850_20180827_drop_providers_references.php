<?php

use yii\db\Migration;

/**
 * Class m180827_114850_20180827_drop_providers_references
 */
class m180827_114850_20180827_drop_providers_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
           USE `" . DB_STORES . "`;
           ALTER TABLE `store_providers` DROP FOREIGN KEY `fk_provider_provider_id`; 
           ALTER TABLE `store_providers` ADD CONSTRAINT `fk_provider_provider_id` FOREIGN KEY (`provider_id`)
           REFERENCES `panels`.`additional_services`(`res`) 
           ON DELETE NO ACTION ON UPDATE NO ACTION;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
           USE `" . DB_STORES . "`;
           ALTER TABLE `store_providers` DROP FOREIGN KEY `fk_provider_provider_id`; 
           ALTER TABLE `store_providers` ADD CONSTRAINT `fk_provider_provider_id` FOREIGN KEY (`provider_id`) 
           REFERENCES `providers`(`id`) 
           ON DELETE NO ACTION ON UPDATE NO ACTION;
        ");
    }
}
