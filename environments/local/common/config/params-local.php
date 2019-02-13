<?php

$serverIp = '188.165.29.223';

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
    'gatewaySqlPath' => Yii::getAlias('@sommerce/runtime/sql/gateway_template.sql'), // Путь к дампу базы данных созданного гейтвея

    'storeDefaultDatabase' => 'store_template', // Шаблонная база данных создаваемых магазинов
    'panelDefaultDatabase' => 'panel_template', // Шаблонная база данных создаваемых панелей
    'gatewayDefaultDatabase' => 'gateway_template', // Шаблонная база данных создаваемых гейтвеев

    'myUrl' => 'http://sommerce.local/', // Полный url раздела My
    'panelDomain' => 'myperfectpanel.local', // Домен нашего сайта
    'storeDomain' => 'sommerce.local', // Домен нашего сайта
    'gatewayDomain' => 'gateway.local', // домен гейтвея

    'nginx_restart' => '/etc/init.d/nginx restart',

    // DNS параметры
    // адрес можно указать наш домен для отладки который возвращает успешное добавление всегда
    'dnsService' => 'http://mydev.perfectpanel.net', // можно указать наш домен для отладки который возвращает успешное добавление всегда
    'dnsId' => '1181',
    'dnsLogin' => '2njujbuhwrZSgW96JynTN7JASe6Q8X64',
    'dnsPassword' => $serverIp,

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

    'store.paywant_proxy' => 'http://37.1.207.99/scr/paywant.php',

    'mailgun.key' => '',
    'mailgun.domain' => 'mail-smm.local',
    'mailgun.timeout' => 25, // В секундах
    'mailer.sendNow' => false,

    'support_email' => 'noreply@mail-smm.local',

    // Данные для Swift mailer
    'swift.host' => 'ssl://smtp.yandex.local',
    'swift.username' => 'noreply@perfectpanel.com',
    'swift.password' => 'aD213kfio34',
    'swift.port' => '465',

    'devEmail' => [], // Адреса почты на которые шлем ошибки
    'failsEmail' => [], // Адреса почты на которые шлем неудачные действия

    'free_ssl.create' => false,  // Создавать заказ/выполнять заказ на бесплатный сертификат или нет
    'free_ssl.prolong' => false, // Создавать заказ/выполнять заказ на продление бесплатного сертификата или нет
];
