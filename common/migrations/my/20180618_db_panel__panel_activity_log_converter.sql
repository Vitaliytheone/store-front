USE `default_panels`;

UPDATE `activity_log` SET `event` =
CASE
  WHEN `event` = 1 THEN 1003
  WHEN `event` = 2 THEN 1004
  WHEN `event` = 3 THEN 1005
  WHEN `event` = 4 THEN 1001
  WHEN `event` = 5 THEN 1006
  WHEN `event` = 6 THEN 1002
  WHEN `event` = 7 THEN 1007
  ELSE `event`
END;