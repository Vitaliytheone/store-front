USE `default_panels`;

UPDATE `activity_log` SET `event` =
CASE
  WHEN `event` = 4 THEN 
  WHEN `event` = 6 THEN
  WHEN `event` = 1 THEN
  WHEN `event` = 2 THEN
  WHEN `event` = 3 THEN
  WHEN `event` = 5 THEN
  WHEN `event` = 7 THEN
END


4	Add user
6	Edit user
1	Generate API key
2	Activate user
3	Suspend user
5	Set password
7	Edit custom rates