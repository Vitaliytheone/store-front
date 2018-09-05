CREATE INDEX idx_customer_id
ON referral_visits (customer_id);

ALTER TABLE `referral_visits` ADD CONSTRAINT `fk_referral_visits_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;