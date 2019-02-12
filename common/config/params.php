<?php

$serverIp = "188.165.29.223";

return [
    'time' => '10800',

    'config.db' => DB_CONFIG,
    'config.proxy' => PROXY_CONFIG,
    'panelNginxConfigPath' => '', // Путь к дирректории где будут храниться конфиги
    'storeNginxConfigPath' => '', // Путь к дирректории где будут храниться конфиги
    'gatewayNginxConfigPath' => '', // Путь к дирректории где будут храниться конфиги
    'panelNginxDefaultConfigPath' => '', // Путь к дирректории где лежит файл default_config.conf
    'storeNginxDefaultConfigPath' => '', // Путь к дирректории где лежит файл default_config.conf
    'gatewayNginxDefaultConfigPath' => '', // Путь к дирректории где лежит файл default_config.conf

    'panelSqlPath' => Yii::getAlias('@sommerce/runtime/sql/panel_template.sql'), // Путь к дампу базы данных соззданной панели
    'storeSqlPath' => Yii::getAlias('@sommerce/runtime/sql/store_template.sql'), // Путь к дампу базы данных созданного магазина
    'gatewaySqlPath' => Yii::getAlias('@sommerce/runtime/sql/gateway_template.sql'),

    'storeDefaultDatabase' => 'store_template', // Шаблонная база данных создаваемых магазинов
    'panelDefaultDatabase' => 'panel_template', // Шаблонная база данных создаваемых панелей
    'gatewayDefaultDatabase' => 'gateway_template',

    'myUrl' => 'http://sommerce.my/', // Полный url раздела My
    'panelDomain' => 'myperfectpanel.com', // Домен нашего сайта
    'storeDomain' => 'sommerce.net', // Домен нашего сайта
    'gatewayDomain' => 'gateway.net',

    'nginx_restart' => '/etc/init.d/nginx restart',

    // DNS параметры
    // адрес можно указать наш домен для отладки который возвращает успешное добавление всегда
    'dnsService' => 'https://api.cloudns.net',
    'dnsId' => '1181',
    'dnsPassword' => '2njujbuhwrZSgW96JynTN7JASe6Q8X64',
    'dnsIp' => $serverIp,

    'panelDeployPrice' => '50',
    'childPanelDeployPrice' => '25',
    'storeDeployPrice' => '35',
    'gatewayDeployPrice' => '50',
    'storeChangeDomainDuration' => 6 * 60 * 60, // Время паузы между сменами домена магазина
    'storeProlongMinDuration' => 14 * 24 * 60 * 60, // 14 дней до окончания действия магазина, в который можно продлить магазин

    'invoice.domainDuration' => 7,
    'invoice.panelDuration' => 7,
    'invoice.sslDuration' => 7,
    'invoice.customDuration' => 7,
    'invoice.storeDuration' => 7,

    'sommerce.twig.cachePath' => '@sommerce/runtime/twig/cache',
    'sommerce.assets.cachePath' => '@sommerce/web/assets',
    'gateway.assets.cachePath' => '@gateway/web/assets',
    'my.assets.cachePath' => '@my/web/assets',

    'ahnames.my.ns' => [
        'ns_1' => 'ns1.perfectdns.com',
        'ns_2' => 'ns2.perfectdns.com',
        'ns_3' => 'ns3.perfectdns.com',
        'ns_4' => null,
    ],

    'ahnames.sommerce.ns' => [
        'ns_1' => 'ns1.sommerce.com',
        'ns_2' => 'ns2.sommerce.com',
        'ns_3' => null,
        'ns_4' => null,
    ],

    'ahnames.gateway.ns' => [
        'ns_1' => 'ns1.managerdns.com',
        'ns_2' => 'ns2.managerdns.com',
        'ns_3' => null,
        'ns_4' => null,
    ],

    'namesilo.my.ns' => [
        'ns1' => 'ns1.perfectdns.com',
        'ns2' => 'ns2.perfectdns.com',
        'ns3' => 'ns3.perfectdns.com',
        'ns4' => null,
    ],

    'namesilo.sommerce.ns' => [
        'ns_1' => 'ns1.sommerce.com',
        'ns_2' => 'ns2.sommerce.com',
        'ns_3' => null,
        'ns_4' => null,
    ],

    'namesilo.gateway.ns' => [
        'ns_1' => 'ns1.perfectdns.com',
        'ns_2' => 'ns2.perfectdns.com',
        'ns_3' => null,
        'ns_4' => null,
    ],

    'my.domains.stop_words' => [
        'perfect'
    ],

    'my.domains.stop_zones' => [
        'tk'
    ],

    // Default store attributes
    'store.defaults' => [
        'timezone' => 0,
        'language' => 'en',
        'theme_name' => 'SMM24',
        'theme_folder' => 'smm24',
        'block_slider' => 1,
        'block_features' => 1,
        'block_reviews' => 1,
        'block_process' => 1,
    ],

    // Default gateway attributes
    'gateway.defaults' => [
        'theme_name' => 'Default',
        'theme_folder' => 'default',
    ],

    'store.staff_users.limit' => 10,

    'project.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление панели
    'domain.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление панели
    'ssl.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление панели
    'store.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление магазин
    'gateway.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление gateway

    'store.paywant_proxy' => 'http://37.1.207.99/scr/paywant.php',

    'mailgun.key' => 'key-cf10921abd5862ddd4b4b55692031fad',
    'mailgun.domain' => 'mail-smm.com',
    'mailgun.timeout' => 25, // В секундах
    'mailer.sendNow' => true,

    'gearmanIp' => '127.0.0.1',
    'gearmanPort' => 4730,
    'gearmanPrefix' => 'stores_',

    'support_email' => 'noreply@mail-smm.com',

    // Swift mailer
    'swift.host' => 'ssl://smtp.yandex.ru',
    'swift.username' => 'noreply@perfectpanel.com',
    'swift.password' => 'aD213kfio34',
    'swift.port' => '465',

    'devEmail' => ['myerror@13.uz'], // Адреса почты на которые шлем ошибки
    'failsEmail' => ['myerror@13.uz'], // Адреса почты на которые шлем неудачные действия - пока не используется нигде

    'cron.check_payments_fee_days' => 2,

    'letsencrypt' => [
        'prolong.days.before' => 20, // За 20 дней до окончания создается заказ на продление
        'paths' => [
            'lib' => Yii::getAlias('@project_root/shell/acme.sh'),
            'ssl' => Yii::getAlias('@project_root/ssl'),
        ],
    ],

    'whoisxmlapi' => [
        'api_url' => 'https://www.whoisxmlapi.com/whoisserver/WhoisService',
    ],

    'dns.checker.records' => [
        'A' => [
            'ip' => $serverIp,
        ],
        'CNAME' => [
            'target' => 'perfectpanel.com',
        ],
    ],

    // Параметры по умолчанию при создании панели
    'projectDefaults' => [
        'theme_path' => 'default_light',
        'lang' => 'en',
        'plan' => 1,
        'comments' => 1,
        'mentions_wo_hashtag' => 1,
        'mentions' => 1,
        'mentions_custom' => 1,
        'mentions_hashtag' => 1,
        'mentions_follower' => 1,
        'mentions_likes' => 1,
        'start_count' => 1,
        'custom' => 1,
        'ticket_per_user' => 3,
        'affiliate_minimum_payout' => 10,
        'affiliate_commission_rate' => 5,
        'affiliate_approve_payouts' => 0,
    ],
];
