USE `stores`;

CREATE TABLE integrations (
  id int NOT NULL PRIMARY KEY,
  category varchar(255) NOT NULL,
  code varchar(255) NOT NULL UNIQUE,
  name varchar(255),
  widget_class varchar(255),
  settings_form text,
  settings_description text,
  visibility tinyint(1) NOT NULL DEFAULT 0 COMMENT '1- видима для всех, 0 - не видима для всех',
  position int,
  created_at int,
  updated_at int
);