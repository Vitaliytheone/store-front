CREATE INDEX idx_cid
ON project (cid);

ALTER TABLE `project` ADD CONSTRAINT `fk_project__customers` FOREIGN KEY (`cid`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;