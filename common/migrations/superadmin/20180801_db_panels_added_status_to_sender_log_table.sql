USE `panels`;

ALTER TABLE `sender_log`
ADD `status` tinyint(1) NULL COMMENT '1 - Success; 2 - Error; 3 - Curl error' AFTER `send_method`;
