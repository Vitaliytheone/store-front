# Update payment_gateways table

USE `stores`;
INSERT INTO `payment_gateways` (`method`, `currencies`, `name`)
VALUES
  ('webmoney', '["RUB"]', 'WebMoney'),
  ('yandexmoney', '["RUB"]', 'Yandex.Money'),
  ('freekassa', '["RUB"]', 'Free-Kassa'),
  ('paytr', '["TRY"]', 'PayTR'),
  ('paywant', '["TRY"]', 'PayWant'),
  ('pagseguro', '["BRL"]', 'PagSeguro'),
  ('billplz', '["MYR"]', 'Billplz');