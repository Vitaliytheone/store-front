ALTER TABLE store_payment_methods
  CHANGE `details` `options` text NULL;

ALTER TABLE store_payment_methods
  CHANGE `active` `visibility` smallint(1) NOT NULL DEFAULT 1;

ALTER TABLE store_payment_methods
  CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE store_payment_methods
  CHANGE `store_id` `store_id` INT(11) NOT NULL;

ALTER TABLE store_payment_methods
  ADD `method_id` int(11) unsigned;

ALTER TABLE store_payment_methods
  ADD `currency_id` int(11) unsigned NULL;

ALTER TABLE store_payment_methods
  ADD `name` varchar(255);

ALTER TABLE store_payment_methods
  ADD `position` int(11);

ALTER TABLE store_payment_methods
  ADD `created_at` int(11) NULL;

ALTER TABLE store_payment_methods
  ADD `updated_at` int(11) NULL;

ALTER TABLE `store_payment_methods`
  DROP INDEX `fk_store_id_method_idx`;

CREATE INDEX idx_store_id
  ON `store_payment_methods` (store_id);

CREATE INDEX idx_method_id
  ON `store_payment_methods` (method_id);

CREATE INDEX idx_currency_id
  ON `store_payment_methods` (currency_id);

ALTER TABLE `store_payment_methods`
  ADD CONSTRAINT `fk_method_id_to_payment_methods` FOREIGN KEY (`method_id`) REFERENCES `payment_methods`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `store_payment_methods`
  ADD CONSTRAINT `fk_currency_id_to_methods_currency` FOREIGN KEY (`currency_id`) REFERENCES `payment_methods_currency`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `store_payment_methods`
  ADD CONSTRAINT `fk_store_id_to_stores` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;