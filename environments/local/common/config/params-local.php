<?php

return [
    'time' => '10800',

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
    'gatewayDefaultDatabase' => 'gateway_template', // Шаблонная база данных создаваемых гейтвеев

    'myUrl' => 'http://sommerce.local/', // Полный url раздела My
    'panelDomain' => 'myperfectpanel.local', // Домен нашего сайта
    'storeDomain' => 'sommerce.local', // Домен нашего сайта
    'gatewayDomain' => 'gateway.local',

    'nginx_restart' => '/etc/init.d/nginx restart',

    // DNS параметры
    // адрес можно указать наш домен для отладки который возвращает успешное добавление всегда
    'dnsService' => 'http://mydev.perfectpanel.net', // можно указать наш домен для отладки который возвращает успешное добавление всегда
    'dnsId' => '',
    'dnsLogin' => '',
    'dnsPassword' => '',

    'panelDeployPrice' => '50',
    'childPanelDeployPrice' => '25',
    'storeDeployPrice' => '35',
    'gatewayDeployPrice' => '50',


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
