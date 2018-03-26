CREATE TABLE `my_customers_hash` (`id` int(11) UNSIGNED NOT NULL,`customer_id` int(11) NOT NULL,`hash` varchar(64) NOT NULL,`ip` varchar(255) NOT NULL,`remember` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not remember; 1 - remember',`super_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - user, 1 - super user',`updated_at` int(11) NOT NULL,`created_at` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `my_customers_hash` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `hash` (`hash`), ADD KEY `user_id` (`customer_id`);

ALTER TABLE `my_customers_hash`  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;

