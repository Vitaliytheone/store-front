USE `stores`;

INSERT INTO `payment_gateways` (`method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`) VALUES
('yandexcards', '[\"RUB\"]', 'Yandex.Cards', 'Yandexcards', 'yandexcards', '14', '{\"wallet_number\":\"\",\"secret_word\":\"\",\"test_mode\":1}');