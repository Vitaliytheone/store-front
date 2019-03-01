ALTER TABLE `stores`
  ADD `whois_lookup` TEXT NULL DEFAULT NULL COMMENT 'Json domain data',
  ADD `nameservers` TEXT NULL DEFAULT NULL COMMENT 'Json domain nameservers data',
  ADD `dns_checked_at` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Last dns-check timestamp',
  ADD `dns_status` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns';
