-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Фев 28 2019 г., 09:33
-- Версия сервера: 10.1.33-MariaDB
-- Версия PHP: 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Структура таблицы `content`
--

CREATE TABLE `content` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `content`
--

INSERT INTO `content` (`id`, `name`, `text`, `updated_at`) VALUES
(1, 'paypal_note', 'контент\r\n\r\nЕще контент1', 1544708021),
(2, '2checkout_note', '', 0),
(3, 'bitcoin_note', '', 0),
(4, 'paypal_hold', '', 0),
(5, '2checkout_review', '', 0),
(6, 'bitcoin_not_confirmed', '', 0),
(7, 'support', 'текст', 1521730467),
(8, 'nameservers', 'some text', 0),
(9, 'forgot_email_sent', 'Good', 1521790513),
(10, 'store_nameservers', '<p class=\"help-block\" style=\"margin-bottom: 5px\">Please visit your registrar\'s dashboard to change nameservers to:</p>\r\n <ul style=\"color: #737373; padding-left: 20px\">\r\n <li>ns1.sommerce.com\r\n </li>\r\n <li>ns2.sommerce.com\r\n </li>\r\n </ul>', 0),
(11, 'paypal_verify_note', 'Please check your email <strong>`email`</strong> and follow payment approve link', 0),
(15, 'subdomain_nameservers', '<p class=\"help-block\" style=\"margin-bottom: 5px\">Please visit your domain\'s DNS zone editor and set CNAME-record:</p>\r\n subdomain.yourdomain.com CNAME perfectpanel.com', 0),
(16, 'gateways_nameservers', '<p class=\"help - block\" style=\"margin - bottom: 5px\">Please visit your registrar\'s dashboard to change nameservers to:</p><ul style=\"color: #737373; padding-left: 20px\"><li>ns1.managerdns.com</li><li>ns2.managerdns.com</li></ul>', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `email` varchar(300) CHARACTER SET utf8 NOT NULL,
  `password` varchar(64) CHARACTER SET utf8 NOT NULL,
  `first_name` varchar(300) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(300) CHARACTER SET utf8 NOT NULL,
  `access_token` varchar(64) CHARACTER SET utf8 NOT NULL,
  `token` varchar(32) CHARACTER SET utf8 NOT NULL,
  `status` int(11) NOT NULL,
  `child_panels` tinyint(1) NOT NULL,
  `stores` tinyint(1) NOT NULL DEFAULT '0',
  `buy_domain` tinyint(1) DEFAULT '0',
  `date_create` int(11) NOT NULL,
  `auth_date` int(11) NOT NULL,
  `auth_ip` varchar(100) CHARACTER SET utf8 NOT NULL,
  `timezone` int(11) NOT NULL,
  `auth_token` varchar(64) NOT NULL,
  `unpaid_earnings` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `referrer_id` int(11) UNSIGNED DEFAULT NULL,
  `referral_status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 - not active, 1 - active, 2 - blocked',
  `paid` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 - not paid, 1 - paid',
  `referral_link` varchar(5) DEFAULT NULL,
  `referral_expired_at` int(11) UNSIGNED DEFAULT NULL,
  `gateway` smallint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `customers_counters`
--

CREATE TABLE `customers_counters` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `stores` int(11) NOT NULL DEFAULT '0',
  `panels` int(11) NOT NULL DEFAULT '0',
  `child_panels` int(11) NOT NULL DEFAULT '0',
  `gateways` int(11) NOT NULL DEFAULT '0',
  `domains` int(11) NOT NULL DEFAULT '0',
  `ssl_certs` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `customers_note`
--

CREATE TABLE `customers_note` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `note` varchar(1000) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `domains`
--

CREATE TABLE `domains` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `contact_id` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1 – ok, 2 - expired',
  `domain` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` int(11) NOT NULL,
  `expiry` int(11) NOT NULL,
  `privacy_protection` tinyint(1) NOT NULL,
  `transfer_protection` tinyint(1) NOT NULL,
  `details` text NOT NULL,
  `registrar` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `domain_zones`
--

CREATE TABLE `domain_zones` (
  `id` int(11) NOT NULL,
  `zone` varchar(250) NOT NULL,
  `price_register` decimal(10,2) NOT NULL,
  `price_renewal` decimal(10,2) NOT NULL,
  `price_transfer` decimal(10,2) NOT NULL,
  `registrar` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `domain_zones`
--

INSERT INTO `domain_zones` (`id`, `zone`, `price_register`, `price_renewal`, `price_transfer`, `registrar`) VALUES
(1, '.COM', '7.00', '7.00', '7.00', 'ahnames'),
(2, '.NET', '7.00', '7.00', '7.00', 'ahnames'),
(3, '.ORG', '7.00', '7.00', '7.00', 'ahnames'),
(4, '.XYZ', '2.00', '2.00', '2.00', 'namesilo');

-- --------------------------------------------------------

--
-- Структура таблицы `expired_log`
--

CREATE TABLE `expired_log` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `expired_last` int(11) NOT NULL,
  `expired` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `code` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `date_update` int(11) NOT NULL,
  `expired` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `credit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` int(11) NOT NULL COMMENT '0 - unpaid; 1 - paid; 2 - canceled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `description` text,
  `amount` decimal(10,2) NOT NULL,
  `item` tinyint(2) NOT NULL COMMENT '1 - buy store; 2 - buy domain; 3 - prolongation domain; 4 - buy sll; 5 - prolongation ssl; 6 - custom customer; 7 - buy trial store; 8 - prolongation store;',
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `project_type` tinyint(1) DEFAULT NULL COMMENT '1-Panel, 2-Store',
  `panel_id` int(11) NOT NULL COMMENT 'panel_id, store_id',
  `data` varchar(1000) NOT NULL,
  `type` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `my_activity_log`
--

CREATE TABLE `my_activity_log` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `super_user` tinyint(1) NOT NULL COMMENT '0 - customer, 1 - super user',
  `created_at` int(11) NOT NULL,
  `ip` varchar(300) NOT NULL,
  `controller` varchar(300) NOT NULL,
  `action` varchar(300) NOT NULL,
  `request_data` text NOT NULL,
  `details` varchar(1000) NOT NULL,
  `details_id` varchar(1000) NOT NULL,
  `event` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `my_customers_hash`
--

CREATE TABLE `my_customers_hash` (
  `id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `remember` varchar(255) NOT NULL COMMENT '0 - not remember; 1 - remember',
  `super_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - user, 1 - super user',
  `updated_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `my_verified_paypal`
--

CREATE TABLE `my_verified_paypal` (
  `id` int(11) UNSIGNED NOT NULL,
  `payment_id` int(11) UNSIGNED NOT NULL,
  `paypal_payer_id` varchar(100) DEFAULT NULL COMMENT 'Payer id from GetTransactionDetails.PAYERID',
  `paypal_payer_email` varchar(300) DEFAULT NULL COMMENT 'Payer email from GetTransactionDetails.EMAIL',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item` tinyint(2) NOT NULL COMMENT '1 - panel, 2 - ssl, 3 - domain',
  `type` varchar(250) NOT NULL,
  `response` text NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `notification_email`
--

CREATE TABLE `notification_email` (
  `id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `message` longtext NOT NULL,
  `code` varchar(300) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 - pending; 1 - paid;2 - added; 3 - error; 4 - canceled',
  `hide` tinyint(1) DEFAULT '0',
  `processing` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `ip` varchar(300) NOT NULL,
  `domain` varchar(300) DEFAULT NULL,
  `details` text NOT NULL,
  `item` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 - buy store; 2 - buy domain; 3 - buy ssl; 4 - prolongation ssl; 5 - prolongation domain; 6 - buy trial store; 7 - free ssl; 8 - prolongation free ssl;',
  `item_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `order_logs`
--

CREATE TABLE `order_logs` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `domain` varchar(300) NOT NULL,
  `date` int(11) NOT NULL,
  `log` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `params`
--

CREATE TABLE `params` (
  `id` int(11) NOT NULL,
  `category` varchar(64) DEFAULT NULL,
  `code` varchar(64) NOT NULL,
  `options` text NOT NULL,
  `updated_at` int(11) NOT NULL,
  `position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `params`
--

INSERT INTO `params` (`id`, `category`, `code`, `options`, `updated_at`, `position`) VALUES
(1, 'service', 'whoisxml', '{\"apiKey\":\"at_29qGYvNzpRjONBzQhtHUpafTaKtud\",\"whoisxml.url\":\"\r\nhttps://www.whoisxmlapi.com/whoisserver/WhoisService\",\"dnsLogin\":\"\",\"dnsPasswd\":\"\"}', 1530199492, NULL),
(2, 'service', 'dnslytics', '{\"dnslytics.apiKey\":\"\",\"dnslytics.url\":\"\"}', 1530199492, NULL),
(3, 'service', 'gogetssl', '{\"goGetSSLUsername\":\"\",\"goGetSSLPassword\":\"\",\"testSSL\":false}', 1530199492, NULL),
(4, 'service', 'ahnames', '{\"ahnames.url\":\"https://demo-api.ahnames.com\",\"ahnames.login\":\"demo\",\"ahnames.password\":\"demo\"}', 1530199492, NULL),
(5, 'service', 'opensrs', '{\"openSRS.ip\":\"\"}', 1530199492, NULL),
(6, 'payment', 'paypal', '{\"code\":\"paypal\",\"name\":\"PayPal\",\"visibility\":\"1\",\"credentials\":{\"email\":\"paypal-facilitator@13.uz\",\"username\":\"paypal-facilitator_api1.13.uz\",\"password\":\"6LQ5TMP6C4HFF6NG\",\"signature\":\"AFcWxV21C7fd0v3bYYYRCpSSRl31AZufRwNv23nT1BBBXjdpsAqzSSqv\"}}', 1539353360, 2),
(7, 'payment', 'perfect_money', '{\"code\":\"perfect_money\",\"name\":\"Perfect Money\",\"visibility\":\"1\",\"credentials\":{\"account\":\"\",\"passphrase\":\"\"}}', 1539353360, 3),
(8, 'payment', 'webmoney', '{\"credentials\":{\"purse\":\"\",\"secret_key\":\"\"},\"name\":\"WebMoney\",\"minimal\":\"0.00\",\"maximal\":\"0.00\",\"visibility\":1,\"fee\":0,\"type\":0,\"dev_options\":\"\"}', 1539353360, 4),
(9, 'payment', 'bitcoin', '{\"code\":\"bitcoin\",\"name\":\"Bitcoin\",\"visibility\":\"1\",\"credentials\":{\"id\":\"\",\"secret\":\"\"}}', 1539353360, 5),
(10, 'payment', '2checkout', '{\"code\":\"2checkout\",\"name\":\"2Checkout\",\"visibility\":\"1\",\"credentials\":{\"account_number\":\"901402179\",\"secret_word\":\"ZGVkMDI1YTgtZDg4OC00ZjUyLTg5MGQtMjhiMjlhYzljYTRk\"}}', 1539353360, 1),
(11, 'payment', 'coinpayments', '{\"credentials\":{\"merchant_id\":\"c1de6e0408c209e599bad925ad8396f2\",\"secret\":\"EKNc0ylbRa3IJbjb\"},\"name\":\"CoinPayments\",\"minimal\":\"0.00\",\"maximal\":\"0.00\",\"visibility\":1,\"fee\":0,\"type\":0,\"dev_options\":\"\"}', 1539353360, 6),
(12, 'service', 'whoisxmlapi', '{\"new_order_form\": \"\", \"ssl\": \"\", \"active_panel\": \"\"}', 0, NULL),
(13, 'service', 'letsencrypt', '{\"account_key\":\"-----BEGIN RSA PRIVATE KEY-----\\nMIIEpAIBAAKCAQEAzUTYOc1gqVzDDcMgwpgc5D24Lnsgm+oURxkj1Z\\/XXxYqOwE1\\nd4H7wWMmF9zzwRsSEMeoZqUkixGc5lEJpg6LRH89dqAaW7bQwJE3w2Xax8xxkemO\\nWDerPGzkccaMTWZEcWKPWv+89c\\/jhEr\\/Gk9eWp4aBlIGlmP5jS+lFg+Q673B\\/fsr\\n98fpTbtNbfJ+jevmeJC761HCwVgC0Nr285qha3bhZhBxjjq\\/i2\\/1gDG2vlimvsnd\\n0mEK7zgDc4Zb5Vf94nBfdvr0wkbs4kNo6d3r2tuxm0PLDdBP58hGEtfYuLETH6tQ\\n\\/AnccaaY04\\/u+b0g79hWaa9qDc31FPwuGkFpUwIDAQABAoIBAQC+sF\\/2gpZzf1ss\\nY8MBQ3JDjhqWA4gtj207B13EzHK1QNAGdH8JAFWyN7thm79N+ynzMKd+g5fJIZmS\\nVGIUQ5qZDWM52k3iOZj\\/62fUO8Dcr04p8MBtr3mB7t7h20LnfEPE2Xy3WrBd4rxH\\nX6xQ8r6CjDE9AeJgv4tK36ILNna4p6suuXQZgGsQNHgtm8rfvFSRN\\/ESyxMpii47\\nliPf9Aquk7mVOkW+70xzXJfm9k4QrctKAhmf6UU1TtjkF7Y\\/My7VHigIBFRaJbV\\/\\ndfRiKDFiITeWxGQXaZY9qKJ1jSBpOAl\\/LHwVROb2zRTpjbdsHujpFgcvbDXwS6hS\\nsuFuKwkhAoGBAPCrzwZ52eAlCRnXe3p4\\/4bWMsMJ3JNOIIkK+isSrDGppEx5KI3L\\nPY0yCV+pegjd1UDqTVWD6VLT1KBYgf5p2ZQPnNg4a3XBD\\/Dmd1jqC1n+Y14wFp0B\\nYAk3BFkZpfgp9si77opgSQE068yXdTnRKcC9RMuBzjhZfbHZ6xuQT4YbAoGBANpX\\ny74S3RZ8WXXsWFGHW4x1vA8N06bdiPd7d6kUMF2y6RO5GB2DtcI+uIHkjPQcW3xE\\nmU\\/gl00kNbRqQlwxHud2njkEOrDn7TDKqVbmrvkwocL9h9\\/\\/t05WjPnj09cHItQk\\n26r1VFYlegTc9EeSfp3D28oOfb5c8DZ0cxH6ub0pAoGAXskbsQ4+e+O6MN5H+FU9\\nNgqYVW5F3BISCEc1fc9N2AVa+u9gxG9+H2TMgkuKD4HmojllGb\\/pHcDl5fzVvbBM\\ni96WRCX8VJUjxRnPeUo8DEZ\\/NjI0RcOr4hUz9i9+yT0lv6scaI1BU2NXVu1zssCo\\n9ArW1FucL183fNs5mFJ+r28CgYAGqqGU5xyLADb3C8VSj\\/BypHGegAyTTrZM4B8y\\nMWScp8bIDGG5HPliuemGRcUr+uWsMKgBsVjNSSq+nHP0Pqez54JDOWbVWe97CuUK\\nuzZic2KtAnKwmy0sniXlx1gDe7tLgOiYGq99hd1o4pouyUFGbeF7FOAv\\/MR01S9i\\nQCaFqQKBgQDDXvHO6pH9+VytiVhKc8DGmSmWoaMUCZaPdKAboeutGrPiGboLDt3N\\nSOJ3D0AIx6hSh6myoUIoBuwVDoLaPeNlP3ikwvVm28nALM8IvvKVyFIJ6xgU7hNn\\nWDIQQE2Gi7IeqWfKvE1qT6gi6NVxHWXUCqGOXKo9CH0+NjHP1\\/JbGg==\\n-----END RSA PRIVATE KEY-----\",\"account_thumbprint\":\"UDTlmgtoemHP9VZUWmXZnXcnQIBpruncJBWJnsIkoMI\"}', 1541058384, NULL),
(14, 'service', 'socialsapi', '{\"apikey\":\"\"}', 1540386666, NULL),
(15, 'service', 'namesilo', '{\"namesilo.url\":\"https://www.namesilo.com/api\",\"namesilo.key\":\"6f3bb35a23962b15be15c3c\",\"namesilo.payment_id\":\"485\",\"namesilo.version\":\"1\",\"namesilo.type\":\"xml\",\"namesilo.testmode\":\"0\",\"namesilo.contact_id\":\"\"}', 1548833103, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `iid` int(11) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `transaction_id` varchar(300) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL,
  `date_update` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `payment_method` varchar(64) DEFAULT NULL,
  `amount` decimal(10,5) NOT NULL,
  `fee` decimal(10,5) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `ip` varchar(300) NOT NULL,
  `response` int(11) NOT NULL,
  `mode` tinyint(4) NOT NULL,
  `options` text NOT NULL,
  `verification_code` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `payments_log`
--

CREATE TABLE `payments_log` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `response` varchar(10000) NOT NULL,
  `logs` varchar(10000) NOT NULL,
  `date` int(11) NOT NULL,
  `ip` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `payment_hash`
--

CREATE TABLE `payment_hash` (
  `id` int(11) NOT NULL,
  `hash` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `reports`
--

CREATE TABLE `reports` (
  `id` int(11) UNSIGNED NOT NULL,
  `service_id` int(11) UNSIGNED NOT NULL,
  `timezone` mediumint(5) NOT NULL COMMENT 'таймзона для которой рассчитан день',
  `date` varchar(10) NOT NULL DEFAULT '' COMMENT 'Y-m-d дня статистики',
  `start` int(11) UNSIGNED NOT NULL COMMENT 'Timestamp of begin statistic calculation',
  `stop` int(11) UNSIGNED NOT NULL COMMENT 'Timestamp of finish statistic calculation',
  `charge` decimal(10,5) DEFAULT NULL COMMENT 'сумма charge',
  `cost` decimal(10,5) DEFAULT NULL COMMENT 'сумма cost',
  `orders` int(11) DEFAULT NULL COMMENT 'количество заказов',
  `quantity` int(11) DEFAULT NULL COMMENT 'сумма quantity',
  `sources` text COMMENT 'json массив, разбивка по orders.api с количеством заказов'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `ssl_cert`
--

CREATE TABLE `ssl_cert` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `project_type` tinyint(1) DEFAULT NULL COMMENT '1 - Panel, 2 - Store',
  `pid` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL COMMENT '0 - Pending; 4 - Cancel; 1 - Active; 2 - Processing; 3 - Processing(payment needed); 5 - Incomplete; 6 - Expiry; 7 - ddos guard error',
  `checked` tinyint(1) NOT NULL COMMENT '0 - unchecked; 1 - checked',
  `domain` varchar(255) NOT NULL,
  `csr_code` text,
  `csr_key` text,
  `csr_files` text COMMENT 'All genered csr files content',
  `details` text NOT NULL,
  `expiry` varchar(10) DEFAULT NULL,
  `expiry_at_timestamp` int(11) UNSIGNED DEFAULT NULL COMMENT 'Expiry date in timestamp format',
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `ssl_cert_item`
--

CREATE TABLE `ssl_cert_item` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `allow` text COMMENT 'List of ids of allowed users for this cert. Allowed for all if NULL',
  `generator` tinyint(1) DEFAULT NULL,
  `provider` tinyint(1) DEFAULT NULL COMMENT '1 - gogetssl, 2 - letsencrypt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ssl_cert_item`
--

INSERT INTO `ssl_cert_item` (`id`, `name`, `product_id`, `price`, `allow`, `generator`, `provider`) VALUES
(0, 'Letsencrypt', 1, '0.00', NULL, NULL, 2),
(1, 'Comodo Positive SSL', 45, '9.95', NULL, 1, 2),
(2, 'Comodo Essential SSL', 75, '19.95', NULL, 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `ssl_validation`
--

CREATE TABLE `ssl_validation` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `super_admin`
--

CREATE TABLE `super_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` int(11) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `last_login` varchar(250) DEFAULT NULL,
  `last_ip` varchar(250) DEFAULT NULL,
  `auth_key` varchar(250) NOT NULL,
  `rules` varchar(1000) DEFAULT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0 – suspended; 1 - active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `super_admin`
--

INSERT INTO `super_admin` (`id`, `username`, `password`, `created_at`, `first_name`, `last_name`, `last_login`, `last_ip`, `auth_key`, `rules`, `status`) VALUES
(1, 'admin', 'b8debceae0c4b8a60048e41d3b90c451bb437c4a157f8e550c2958fec15e9edc', 1538810893, 'admin', 'admin', '1551274927', '127.0.0.1', '', '{\"panels\":\"1\",\"orders\":\"1\",\"domains\":\"1\",\"ssl\":\"1\",\"customers\":\"1\",\"invoices\":\"1\",\"payments\":\"1\",\"tickets\":\"1\",\"providers\":\"1\",\"referrals\":\"1\",\"reports\":\"1\",\"statuses\":\"1\",\"logs\":\"1\",\"staffs\":\"1\",\"settings\":\"1\",\"tools\":\"1\",\"fraud\":\"1\",\"gateways\":\"1\"}', 1);
-- --------------------------------------------------------

--
-- Структура таблицы `super_admin_token`
--

CREATE TABLE `super_admin_token` (
  `id` int(11) NOT NULL,
  `super_admin_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - panels, 1 - my, 2 - sommerce admin',
  `token` varchar(64) NOT NULL,
  `expiry_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `super_credits_log`
--

CREATE TABLE `super_credits_log` (
  `id` int(11) NOT NULL,
  `super_admin_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `credit` decimal(10,2) NOT NULL,
  `memo` varchar(300) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `super_log`
--

CREATE TABLE `super_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(250) NOT NULL,
  `params` varchar(1000) NOT NULL,
  `ip` varchar(250) NOT NULL,
  `user_agent` varchar(1000) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `super_tasks`
--

CREATE TABLE `super_tasks` (
  `id` int(11) NOT NULL,
  `task` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - restart nginx, 2 - create nginx config',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - pending, 1 - completed',
  `item_id` int(11) DEFAULT NULL,
  `comment` varchar(3000) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `done_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `third_party_log`
--

CREATE TABLE `third_party_log` (
  `id` int(11) NOT NULL,
  `item` tinyint(4) NOT NULL COMMENT '1 – buy panel, 2 – prolongation panel, 3 – buy domain, 4 – prolongation domain, 5 – buy ssl certification, 6 – prolongation ssl certification; 7 - order',
  `item_id` int(11) NOT NULL,
  `code` varchar(32) DEFAULT NULL,
  `details` text NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `assigned_admin_id` int(11) DEFAULT NULL,
  `pid` int(11) NOT NULL,
  `subject` varchar(300) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `is_user` tinyint(1) NOT NULL,
  `user_agent` varchar(300) DEFAULT NULL,
  `status` int(11) NOT NULL COMMENT '0 - pending; 1 - respinded; 2 - closed; 3 - in progress; 4 - Solved;',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `ip` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_files`
--

CREATE TABLE `ticket_files` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `cdn_id` varchar(255) NOT NULL,
  `mime` varchar(255) NOT NULL,
  `details` varchar(10000) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_messages`
--

CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_agent` varchar(300) DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT '0',
  `message` varchar(10000) NOT NULL,
  `created_at` int(11) NOT NULL,
  `ip` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrer_id` (`referrer_id`),
  ADD KEY `idx_status` (`status`);

--
-- Индексы таблицы `customers_counters`
--
ALTER TABLE `customers_counters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`);

--
-- Индексы таблицы `customers_note`
--
ALTER TABLE `customers_note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notes_customer_id` (`customer_id`),
  ADD KEY `fk_notes_created_by` (`created_by`),
  ADD KEY `fk_notes_updated_by` (`updated_by`);

--
-- Индексы таблицы `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `zone_id` (`zone_id`),
  ADD KEY `idx_status_created_at` (`status`,`created_at`);

--
-- Индексы таблицы `domain_zones`
--
ALTER TABLE `domain_zones`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `expired_log`
--
ALTER TABLE `expired_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Индексы таблицы `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `my_activity_log`
--
ALTER TABLE `my_activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `my_customers_hash`
--
ALTER TABLE `my_customers_hash`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`),
  ADD KEY `user_id` (`customer_id`);

--
-- Индексы таблицы `my_verified_paypal`
--
ALTER TABLE `my_verified_paypal`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `notification_email`
--
ALTER TABLE `notification_email`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domain` (`domain`(255));

--
-- Индексы таблицы `order_logs`
--
ALTER TABLE `order_logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `params`
--
ALTER TABLE `params`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_code` (`code`),
  ADD UNIQUE KEY `uniq_category_code` (`code`,`category`);

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `payments_log`
--
ALTER TABLE `payments_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `payment_hash`
--
ALTER TABLE `payment_hash`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ssl_cert`
--
ALTER TABLE `ssl_cert`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `fk_ssl_cert__customers` (`cid`),
  ADD KEY `idx_status_created_at` (`status`,`created_at`);

--
-- Индексы таблицы `ssl_cert_item`
--
ALTER TABLE `ssl_cert_item`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ssl_validation`
--
ALTER TABLE `ssl_validation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`),
  ADD KEY `file_name` (`file_name`);

--
-- Индексы таблицы `super_admin`
--
ALTER TABLE `super_admin`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `super_admin_token`
--
ALTER TABLE `super_admin_token`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `super_credits_log`
--
ALTER TABLE `super_credits_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `super_log`
--
ALTER TABLE `super_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Индексы таблицы `super_tasks`
--
ALTER TABLE `super_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `third_party_log`
--
ALTER TABLE `third_party_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Индексы таблицы `ticket_files`
--
ALTER TABLE `ticket_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ticket_files_tickets` (`ticket_id`),
  ADD KEY `fk_ticket_files_ticket_message` (`message_id`);

--
-- Индексы таблицы `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `customers_counters`
--
ALTER TABLE `customers_counters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `customers_note`
--
ALTER TABLE `customers_note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `domain_zones`
--
ALTER TABLE `domain_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `expired_log`
--
ALTER TABLE `expired_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=285;

--
-- AUTO_INCREMENT для таблицы `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=307;

--
-- AUTO_INCREMENT для таблицы `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `my_activity_log`
--
ALTER TABLE `my_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `my_customers_hash`
--
ALTER TABLE `my_customers_hash`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=435;

--
-- AUTO_INCREMENT для таблицы `my_verified_paypal`
--
ALTER TABLE `my_verified_paypal`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT для таблицы `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `notification_email`
--
ALTER TABLE `notification_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `params`
--
ALTER TABLE `params`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `payments_log`
--
ALTER TABLE `payments_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `payment_hash`
--
ALTER TABLE `payment_hash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ssl_cert`
--
ALTER TABLE `ssl_cert`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `super_admin`
--
ALTER TABLE `super_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `super_admin_token`
--
ALTER TABLE `super_admin_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT для таблицы `super_credits_log`
--
ALTER TABLE `super_credits_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `super_log`
--
ALTER TABLE `super_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `super_tasks`
--
ALTER TABLE `super_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=512;

--
-- AUTO_INCREMENT для таблицы `third_party_log`
--
ALTER TABLE `third_party_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24808;

--
-- AUTO_INCREMENT для таблицы `ticket_files`
--
ALTER TABLE `ticket_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `customers_counters`
--
ALTER TABLE `customers_counters`
  ADD CONSTRAINT `fk_customers_counters_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Ограничения внешнего ключа таблицы `ticket_files`
--
ALTER TABLE `ticket_files`
  ADD CONSTRAINT `fk_ticket_files_ticket_message` FOREIGN KEY (`message_id`) REFERENCES `ticket_messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ticket_files_tickets` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
