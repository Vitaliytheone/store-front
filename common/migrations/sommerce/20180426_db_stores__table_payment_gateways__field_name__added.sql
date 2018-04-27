# Update payment_gateways table

USE stores;
ALTER TABLE `payment_gateways` ADD `name` VARCHAR(300)  NULL  DEFAULT NULL  AFTER `currencies`;

UPDATE `payment_gateways` SET `name` = 'PayPal' WHERE `method` = 'paypal';
UPDATE `payment_gateways` SET `name` = '2Checkout' WHERE `method` = '2checkout';
UPDATE `payment_gateways` SET `name` = 'CoinPayments' WHERE `method` = 'coinpayments';
