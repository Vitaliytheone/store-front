USE `stores`;

CREATE TABLE store_integrations (
  id int NOT NULL PRIMARY KEY,
  integration_id int,
  store_id int,
  options text,
  visibility tinyint(1) NOT NULL DEFAULT 0 COMMENT '1- активна, 0 - не активна',
  position int,
  created_at int,
  updated_at int,
  CONSTRAINT FK_integration_id FOREIGN KEY (integration_id)
    REFERENCES integrations(id),
  CONSTRAINT FK_store_id FOREIGN KEY (store_id)
    REFERENCES stores(id)
);