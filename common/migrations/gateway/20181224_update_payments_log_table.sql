USE `gateway`;

ALTER TABLE `payments_log`
CHANGE `response` `response` text NULL AFTER `payment_id`,
ADD `result` text NULL AFTER `response`;