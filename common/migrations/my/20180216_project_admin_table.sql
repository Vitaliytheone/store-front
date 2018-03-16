ALTER TABLE project_admin ADD rules_pages TINYINT(1) NOT NULL DEFAULT '0' AFTER rules_themes;
ALTER TABLE project_admin ADD rules_providers TINYINT(1) NOT NULL DEFAULT '0' AFTER rules_pages;