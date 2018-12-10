USE `stores`;

INSERT INTO `payment_gateways` (`id`, `method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`, `visibility`)
VALUES (NULL, 'paypalstandard', '[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\"]',
'PayPal Standard', 'Paypalstandard', 'paypalstandard', '16', '{\\\"email\\\":\\\"\\\",\\\"username\\\":\\\"\\\",\\\"password\\\":\\\"\\\",\\\"signature\\\":\\\"\\\",\\\"test_mode\\\":\\\"\\\"}', '0');
