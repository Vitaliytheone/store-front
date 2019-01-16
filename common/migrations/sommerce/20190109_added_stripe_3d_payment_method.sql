USE `stores`;

INSERT INTO `payment_gateways` (`id`, `method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`, `visibility`) VALUES (NULL, 'stripe_3d_secure', '[\"RUB\", \"USD\"]', 'Stripe 3D Secure', 'Stripe3dSecure', 'stripe_3d_secure', '17', '{\"public_key\":\"\",\"secret_key\":\"\",\"webhook_secret\": \"\"}', '1');