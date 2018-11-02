ALTER TABLE additional_services
DROP PRIMARY KEY;

ALTER TABLE `additional_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `res` (`res`);

ALTER TABLE `additional_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;