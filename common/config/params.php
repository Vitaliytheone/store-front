<?php

$serverIp = "54.37.239.222";

return [
    'time' => '10800',

    'config.db' => DB_CONFIG,
    'config.proxy' => PROXY_CONFIG,
    'panelNginxConfigPath' => '', // Путь к дирректории где будут храниться конфиги
    'storeNginxConfigPath' => '', // Путь к дирректории где будут храниться конфиги
    'panelNginxDefaultConfigPath' => '', // Путь к дирректории где лежит файл default_config.conf
    'storeNginxDefaultConfigPath' => '', // Путь к дирректории где лежит файл default_config.conf

    'panelSqlPath' => Yii::getAlias('@sommerce/runtime/sql/panel_template.sql'), // Путь к дампу базы данных соззданной панели
    'storeSqlPath' => Yii::getAlias('@sommerce/runtime/sql/store_template.sql'), // Путь к дампу базы данных созданного магазина

    'storeDefaultDatabase' => 'store_template', // Шаблонная база данных создаваемых магазинов
    'panelDefaultDatabase' => 'panel_template', // Шаблонная база данных создаваемых панелей

    'myUrl' => 'http://sommerce.my/', // Полный url раздела My
    'panelDomain' => 'myperfectpanel.com', // Домен нашего сайта
    'storeDomain' => 'sommerce.net', // Домен нашего сайта

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
    'storeChangeDomainDuration' => 6 * 60 * 60, // Время паузы между сменами домена магазина
    'storeProlongMinDuration' => 14 * 24 * 60 * 60, // 14 дней до окончания действия магазина, в который можно продлить магазин

    'invoice.domainDuration' => 7,
    'invoice.panelDuration' => 7,
    'invoice.sslDuration' => 7,
    'invoice.customDuration' => 7,
    'invoice.storeDuration' => 7,

    'sommerce.twig.cachePath' => '@sommerce/runtime/twig/cache',
    'sommerce.assets.cachePath' => '@sommerce/web/assets',
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

    'store.staff_users.limit' => 10,

    'project.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление панели
    'domain.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление панели
    'ssl.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление панели
    'store.invoice_prolong' => 7, // За 7 дней до окончания, создается инвойс на продление магазин

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
        'paths' => [
            'lib' => Yii::getAlias('@project_root/shell/acme.sh'),
            'ssl' => Yii::getAlias('@project_root/ssl'),
        ],
    ],

    'cron.check_payments_fee_days' => 2,
    'cron.orderExpiry' => 30, //days

    'whoisxmlapi' => [
        'api_url' => 'https://www.whoisxmlapi.com/whoisserver/WhoisService',
    ],
];
