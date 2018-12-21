CREATE TABLE `admins_hash` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `super_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - admin, 1 - superadmin',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `admins_hash`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hash` (`hash`),
  ADD KEY `__admin_id__hash` (`admin_id`,`hash`),
  ADD KEY `idx_hash` (`hash`),
  ADD KEY `idx_admin_id` (`admin_id`);

ALTER TABLE `store_admins_hash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;