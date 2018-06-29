USE `panels`;

CREATE TABLE `params` (
  `id` int(11) NOT NULL,
  `code`varchar(64) NOT NULL,
  `options` text NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB;

ALTER TABLE `params`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `params`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `params`
  ADD UNIQUE INDEX `uniq_code` (`code`);


INSERT INTO `params` (`id`, `code`, `options`, `updated_at`) VALUES
(1, 'service.whoisxml', '{"whoisxml.url":"","dnsLogin":"","dnsPasswd":""}', UNIX_TIMESTAMP (NOW())),
(2, 'service.dnslytics', '{"dnslytics.apiKey":"","dnslytics.url":""}', UNIX_TIMESTAMP (NOW())),
(3, 'service.gogetssl', '{"goGetSSLUsername":"","goGetSSLPassword":"","testSSL":false}', UNIX_TIMESTAMP (NOW())),
(4, 'service.ahnames', '{"ahnames.url":"","ahnames.login":"","ahnames.password":""}', UNIX_TIMESTAMP (NOW())),
(5, 'service.opensrs', '{"openSRS.ip":""}', UNIX_TIMESTAMP (NOW()));




