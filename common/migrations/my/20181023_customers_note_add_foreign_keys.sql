ALTER TABLE customers_note
  ADD CONSTRAINT `fk_notes_created_by`
    FOREIGN KEY created_by REFERENCES super_admin.id;

ALTER TABLE customers_note
  ADD CONSTRAINT `fk_notes_updated_by`
    FOREIGN KEY updated_by REFERENCES super_admin.id;