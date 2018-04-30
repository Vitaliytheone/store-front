USE `stores`;

ALTER TABLE `payment_gateways`
ADD `class_name` varchar(255) NOT NULL AFTER `name`,
ADD `url` varchar(255) NOT NULL AFTER `class_name`,
ADD `position` tinyint(2) NOT NULL AFTER `url`,
ADD `options` text NOT NULL AFTER `position`;

UPDATE `payment_gateways` SET
`name` = 'PayPal',
`class_name` = 'Paypal',
`url` = 'paypalexpress',
`position` = '1',
`options` = '{\"email\":\"\",\"username\":\"\",\"password\":\"\",\"signature\":\"\",\"test_mode\":\"\"}'
WHERE `method` = 'paypal';

UPDATE `payment_gateways` SET
`name` = '2Checkout',
`class_name` = 'Twocheckout',
`url` = '2checkout',
`position` = '2',
`options` = '{\"account_number\":\"\",\"secret_word\":\"\",\"test_mode\":\"\"}'
WHERE `method` = '2checkout';

UPDATE `payment_gateways` SET
`name` = 'CoinPayments',
`class_name` = 'Coinpayments',
`url` = 'coinpayments',
`position` = '3',
`options` = '{\"merchant_id\":\"\",\"ipn_secret\":\"\",\"test_mode\":\"\"}'
WHERE `method` = 'coinpayments';


UPDATE `payment_gateways` SET
`name` = 'PagSeguro',
`class_name` = 'Pagseguro',
`url` = 'pagseguro',
`position` = '4',
`options` = '{\"email\":\"\",\"token\":\"\",\"test_mode\":1}'
WHERE `method` = 'pagseguro';


UPDATE `payment_gateways` SET
`name` = 'WebMoney',
`class_name` = 'Webmoney',
`url` = 'webmoney',
`position` = '5',
`options` = '{\"purse\":\"\",\"secret_key\":\"\",\"test_mode\":1}'
WHERE `method` = 'webmoney';

UPDATE `payment_gateways` SET
`name` = 'Free-Kassa',
`class_name` = 'Freekassa',
`url` = 'freekassa',
`position` = '6',
`options` = '{\\"merchant_id\\":\\"\\",\\"secret_word\\":\\"\\",\\"secret_word2\\":\\"\\",\\"test_mode\\":1}'
WHERE `method` = 'freekassa';

UPDATE `payment_gateways` SET
`name` = 'Yandex.Money',
`class_name` = 'Yandexmoney',
`url` = 'yandexmoney',
`position` = '7',
`options` = '{\\"wallet_number\\":\\"\\",\\"secret_word\\":\\"\\",\\"test_mode\\":1}'
WHERE `method` = 'yandexmoney';