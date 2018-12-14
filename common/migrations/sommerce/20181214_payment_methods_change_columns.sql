ALTER TABLE payment_methods
  CHANGE `method` `method_name` varchar(255);

ALTER TABLE payment_methods
  CHANGE `options` `settings_form` text NULL;

ALTER TABLE payment_methods
  DROP COLUMN `currencies`;

ALTER TABLE payment_methods
  DROP COLUMN `position`;

ALTER TABLE payment_methods
  ALTER COLUMN `name` varchar(255) NOT NULL;

ALTER TABLE payment_methods
  ADD `addfunds_form` text NULL;

ALTER TABLE payment_methods
  ADD `settings_form_description` text NULL;

ALTER TABLE payment_methods
  ADD `manual_callback_url` smallint(1) NOT NULL DEFAULT 0;

ALTER TABLE payment_methods
  ADD `created_at` int(1) NULL;

ALTER TABLE payment_methods
  ADD `updated_at` int(1) NULL;

ALTER TABLE payment_methods
  ADD `icon` varchar(64);