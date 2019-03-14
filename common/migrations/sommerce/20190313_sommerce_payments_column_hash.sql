ALTER TABLE `payments` ADD `hash` VARCHAR(32)  NULL  DEFAULT NULL  AFTER `country`;
ALTER TABLE `payments` ADD UNIQUE INDEX `idx_unique_hash` (`hash`);
