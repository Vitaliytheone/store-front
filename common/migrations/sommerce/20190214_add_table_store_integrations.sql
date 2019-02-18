USE `stores`;

CREATE TABLE store_integrations (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  integration_id int,
  store_id int,
  options text,
  visibility tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 - active, 0 - inactive',
  position int,
  created_at int,
  updated_at int,
  CONSTRAINT fk_store_integrations_integrations FOREIGN KEY (integration_id)
    REFERENCES integrations(id),
  CONSTRAINT fk_store_integrations_stores FOREIGN KEY (store_id)
    REFERENCES stores(id)
);