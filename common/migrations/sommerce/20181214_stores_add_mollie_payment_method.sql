USE `stores`;

INSERT INTO `payment_gateways` (`id`, `method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`, `visibility`)
VALUES (NULL, 'mollie', '[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\"]',
 'Mollie', 'Mollie', 'mollie', '17', '{\"secret_key\":\"\"}', '1');
