UPDATE `default_themes` SET
`name` = 'SMM24'
WHERE `folder` = 'smm24';

UPDATE `default_themes` SET
`name` = 'Bootstrap'
WHERE `folder` = 'classic';

UPDATE `default_themes` SET
`folder` = 'bootstrap'
WHERE `folder` = 'classic';

UPDATE `stores` SET
`theme_name` = 'Bootstrap',
`theme_folder` = 'bootstrap'
WHERE `theme_folder` = 'classic';