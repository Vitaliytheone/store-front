
ALTER TABLE ticket_notes
ADD CONSTRAINT `fk_notes_customer_id`
FOREIGN KEY customer_id REFERENCES customers.id;

ALTER TABLE ticket_notes
RENAME TO customers_note;