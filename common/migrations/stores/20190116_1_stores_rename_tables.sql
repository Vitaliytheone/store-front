USE `stores`;

ALTER TABLE payment_methods RENAME store_payment_methods;

ALTER TABLE payment_gateways RENAME payment_methods;