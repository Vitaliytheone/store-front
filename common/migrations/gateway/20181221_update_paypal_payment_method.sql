USE `gateways`;

UPDATE `payment_methods` SET
`method_name` = 'Paypal',
`class_name` = 'Paypal',
`url` = 'paypal'
WHERE `id` = '1';