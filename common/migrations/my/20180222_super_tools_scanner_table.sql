RENAME TABLE `super_tools_levopanel` TO `super_tools_scanner`;
ALTER TABLE `super_tools_scanner` ADD `panel_id` INT(11)  UNSIGNED  NULL  COMMENT 'Separated ids for each panels'  AFTER `id`;

/* Update panel_ids of current set of panels */
SET @levopanel_id := 0;
SET @smmfire_id := 0;
UPDATE super_tools_scanner
SET panel_id = IF(panel=1, @levopanel_id:=@levopanel_id+1, @smmfire_id:=@smmfire_id+1);

/* Uncomment bellow string for deleting all old domains with unused statuses */
/* DELETE FROM super_tools_scanner WHERE `status` NOT IN(1,2,3,5); */