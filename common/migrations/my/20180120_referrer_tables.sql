ALTER TABLE customers
ADD unpaid_earnings decimal(20,5) NOT NULL DEFAULT '0',
ADD referrer_id int(11) unsigned NULL AFTER unpaid_earnings,
ADD referral_status tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - not active, 1 - active, 2 - blocked' AFTER referrer_id,
ADD paid tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - not paid, 1 - paid' AFTER referral_status,
ADD referral_link varchar(5) NULL AFTER paid,
ADD referral_expired_at int(11) unsigned NULL AFTER referral_link;

ALTER TABLE customers
ADD INDEX referrer_id (referrer_id);

CREATE TABLE referral_visits (
id int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
customer_id int(11) unsigned NOT NULL,
ip varchar(300) NOT NULL,
user_agent varchar(300) NOT NULL,
http_referer varchar(300) NOT NULL,
request_data text NOT NULL,
created_at int(11) unsigned NOT NULL
) ENGINE='InnoDB';

ALTER TABLE referral_visits
ADD INDEX customer_id (customer_id);

CREATE TABLE referral_earnings (
id int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
customer_id int(11) unsigned NOT NULL,
earnings decimal(20,5) NOT NULL,
invoice_id int(11) unsigned NOT NULL,
status tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - completed, 2 - rejected',
created_at int(11) unsigned NOT NULL,
updated_at int(11) unsigned NULL
) ENGINE='InnoDB';

ALTER TABLE referral_earnings
ADD INDEX customer_id (customer_id),
ADD INDEX invoice_id (invoice_id);