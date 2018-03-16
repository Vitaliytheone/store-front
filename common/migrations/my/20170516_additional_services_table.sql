CREATE TABLE `additional_services` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `res` int(11) NOT NULL,
  `apihelp` varchar(2000) NOT NULL,
  `content` longtext NOT NULL,
  `type` int(11) NOT NULL COMMENT 'Тип процессора 0 - не gyp, 1 - gyp',
  `status` int(11) NOT NULL COMMENT 'Статус работы 0 - работает 1 - не работает, 2 - не доделан',
  `search` int(11) NOT NULL,
  `username` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `skype` varchar(300) NOT NULL,
  `type_name` varchar(300) NOT NULL,
  `sc` int(11) NOT NULL,
  `auto_services` int(11) NOT NULL COMMENT '0 - not auto list, 1 - auto list, 2 - custom string',
  `auto_order` int(11) NOT NULL DEFAULT '1',
  `processing` int(11) NOT NULL DEFAULT '1',
  `show_id` int(11) NOT NULL DEFAULT '1',
  `input_type` int(11) NOT NULL,
  `proxy` varchar(1000) NOT NULL,
  `string_type` int(11) NOT NULL,
  `string_name` int(11) NOT NULL COMMENT '0 - String, 1 - Service, 2 - type, 3 - serviceID, 4 - id, 5 - service, 6 - product_id, 7 - service_id, 8 - string, 9 - category, 10 - ordertype, 11 - package_id, 12 - methodname, 13 - , 14 - orderType, 15 - services, 16 - serviceid, 17 - id_service, 18 - SERVICEID, 19 - o_type, 20 - act, 21 - order_service, 22 - product, 23 - tid, 24 - order_service',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `additional_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `res` (`res`);

ALTER TABLE `additional_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;