USE `panels`;

ALTER TABLE `project`
ADD `no_referral` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - disabled, 1 - enabled';
