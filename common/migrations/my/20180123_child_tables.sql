ALTER TABLE customers ADD child_panels TINYINT(1) NOT NULL AFTER status;

ALTER TABLE project ADD child_panel TINYINT(1) NOT NULL AFTER act, ADD provider_id INT NOT NULL AFTER child_panel;

ALTER TABLE orders CHANGE item item TINYINT(2) NOT NULL DEFAULT '1' COMMENT '1 - buy panel; 2 - buy domain; 3 - buy ssl, 4 - buy child panel';

ALTER TABLE invoice_details CHANGE item item TINYINT(2) NOT NULL COMMENT '1 – buy panel, 2 – prolongation panel, 3 – buy domain, 4 – prolongation domain, 5 – buy ssl certification, 6 – prolongation ssl certification, 7 - buy child panel';