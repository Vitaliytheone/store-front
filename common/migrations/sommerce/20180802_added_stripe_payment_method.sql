USE `stores`;

INSERT INTO `payment_gateways` (`method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`) VALUES
     ('stripe', '[\"RUB\", \"USD\"]', 'Stripe', 'Stripe', 'stripe', '15', '{\"public_key\":\"\",\"secret_key\":\"\",\"webhook_secret\": \"\"}');