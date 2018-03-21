CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_id` int(11) NOT NULL,
  `item` tinyint(2) NOT NULL COMMENT '1 - panel, 2 - ssl, 3 - domain',
  `type` varchar(250) NOT NULL,
  `response` text NOT NULL,
  `date` int(11) NOT NULL
) ENGINE='InnoDB' COLLATE 'utf8_general_ci';