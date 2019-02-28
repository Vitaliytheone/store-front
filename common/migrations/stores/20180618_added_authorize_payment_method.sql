USE `stores`;

INSERT INTO `payment_gateways` (`method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`) VALUES
('authorize',	'[\"USD\"]',	'Authorize',	'Authorize',	'authorize',	10,	'{\"merchant_client_key\":\"\",\"merchant_login_id\":\"\",\"merchant_transaction_id\":\"\"}');