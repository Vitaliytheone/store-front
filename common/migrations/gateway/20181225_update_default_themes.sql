USE `gateways`;

DELETE FROM `default_themes`
WHERE ((`folder` = 'green'));

UPDATE `default_themes` SET
`name` = 'Default',
`folder` = 'default',
WHERE `folder` = 'bootstrap';