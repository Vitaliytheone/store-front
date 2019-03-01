USE `stores`;

UPDATE payment_gateways SET class_name = CONCAT(UCASE(LEFT(class_name, 1)), SUBSTRING(class_name, 2));