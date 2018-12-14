ALTER TABLE store_payment_methods
  CHANGE `details` `options` text NULL;

ALTER TABLE store_payment_methods
  CHANGE `active` `visibility` smallint(1) NOT NULL DEFAULT 1;

ALTER TABLE store_payment_methods
  DROP COLUMN `method`;

ALTER TABLE store_payment_methods
  ALTER COLUMN `id` int(11) unsigned;

ALTER TABLE store_payment_methods
  ALTER COLUMN `store_id` int(11) NOT NULL;

ALTER TABLE store_payment_methods
  ADD `method_id` int(11) unsigned;

ALTER TABLE store_payment_methods
  ADD `currency_id` int(11) unsigned NULL;

ALTER TABLE store_payment_methods
  ADD `name` varchar(255);

ALTER TABLE store_payment_methods
  ADD `position` int(11);

ALTER TABLE store_payment_methods
  ADD `created_at` int(1) NULL;

ALTER TABLE store_payment_methods
  ADD `updated_at` int(1) NULL;