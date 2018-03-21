CREATE TABLE `domain_zones` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `zone` varchar(250) NOT NULL,
  `price_register` decimal(10,2) NOT NULL,
  `price_renewal` decimal(10,2) NOT NULL,
  `price_transfer` decimal(10,2) NOT NULL
) ENGINE='InnoDB' COLLATE 'utf8_general_ci';