ALTER TABLE `ssl_cert` ADD `checked` tinyint(1) NOT NULL COMMENT '0 - unchecked; 1 - checked' AFTER `status`;
ALTER TABLE `ssl_cert` CHANGE `status` `status` tinyint(2) NOT NULL COMMENT '0 - Pending; 4 - Cancel; 1 - Active; 2 - Processing; 3 - Processing(payment needed); 5 - Incomplete; 6 - Expiry; 7 - ddos guard error' AFTER `item_id`;

