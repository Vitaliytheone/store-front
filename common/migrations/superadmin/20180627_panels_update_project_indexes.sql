USE `panels`;

ALTER TABLE `project`
  ADD INDEX `idx_cid` (`cid`);

ALTER TABLE `project`
  ADD INDEX `idx_act_date_child_panel` (`act`,`date`,`child_panel`);

