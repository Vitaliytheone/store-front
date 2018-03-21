CREATE TABLE `panel_providers_log` (   `id` int(11) NOT NULL,   `panel_id` int(11) NOT NULL,   `admin_id` int(11) NOT NULL,   `provider_id` int(11) NOT NULL,   `login` varchar(1300) NOT NULL,   `passwd` varchar(300) NOT NULL,   `apikey` varchar(300) NOT NULL,   `matched` varchar(300) DEFAULT NULL,   `report` tinyint(1) NOT NULL,   `created_at` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `panel_providers_log`   ADD PRIMARY KEY (`id`);
ALTER TABLE `panel_providers_log`   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
