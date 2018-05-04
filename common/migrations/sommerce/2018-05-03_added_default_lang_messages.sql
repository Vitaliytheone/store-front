USE `stores`;

INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
VALUES ('en', 'cart', 'payment_description', 'Order #{order_id}');

INSERT INTO `store_default_messages` (`lang_code`, `section`, `name`, `value`)
VALUES ('en', 'order', 'invalid_link', 'Invalid {name} link.');