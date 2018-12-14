CREATE TABLE payment_methods_currency (
  id int(11) NOT NULL,
  method_id int(11) unsigned,
  currency char(3),
  position int(11),
  settings_form text DEFAULT NULL,
  settings_form_description text DEFAULT NULL,
  hidden smallint(1) DEFAULT 0,
  created_at int(11),
  updated_at int(11)
);

ALTER TABLE payment_methods_currency
  ADD PRIMARY KEY (id);

CREATE INDEX idx_method_id
ON payment_methods_currency (method_id);

ALTER TABLE payment_methods_currency
  ADD FOREIGN KEY (method_id) REFERENCES payment_methods(id);