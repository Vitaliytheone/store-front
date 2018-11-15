ALTER TABLE `project` ADD `whois_lookup` TEXT  NULL  COMMENT 'Json domain data'  AFTER `refiller`;
ALTER TABLE `project` ADD `nameservers` TEXT  NULL COMMENT 'Json domain nameservers data'  AFTER `whois_lookup`;
ALTER TABLE `project` ADD `dns_checked_at` INT(11)  UNSIGNED  NULL  COMMENT 'Last dns-check timestamp'  AFTER `nameservers`;
ALTER TABLE `project` ADD `dns_status` TINYINT(1)  UNSIGNED  NULL  DEFAULT NULL  COMMENT 'dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns'  AFTER `dns_checked_at`;