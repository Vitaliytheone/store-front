ALTER TABLE provider_search_log
CHANGE uid admin_id int(11);

ALTER TABLE provider_search_log
CHANGE pid panel_id int(11);

ALTER TABLE provider_search_log
CHANGE `date` `created_at` int(11);