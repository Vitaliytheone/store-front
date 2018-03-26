CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(300) NOT NULL,
  `title` text NOT NULL,
  `message` longtext NOT NULL,
  `code` varchar(300) NOT NULL
) ENGINE='InnoDB' COLLATE 'utf8_general_ci';

INSERT INTO `notifications` (`name`, `title`, `message`, `code`)
VALUES ('SSL - Action needed', '', '', 'ssl_action_needed');