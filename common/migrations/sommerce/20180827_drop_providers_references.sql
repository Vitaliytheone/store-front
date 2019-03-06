USE `stores`;
ALTER TABLE `store_providers` DROP FOREIGN KEY `fk_provider_provider_id`;
ALTER TABLE `store_providers` ADD CONSTRAINT `fk_provider_provider_id` FOREIGN KEY (`provider_id`)
REFERENCES `panels`.`additional_services`(`res`)
ON DELETE NO ACTION ON UPDATE NO ACTION;