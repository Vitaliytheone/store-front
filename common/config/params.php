<?php

$serverIp = "137.74.23.77";

return [
    'config.db' => DB_CONFIG,
    'config.proxy' => PROXY_CONFIG,
    'panelNginxConfigPath' => '', // Путь к дирректории где будут храниться конфиги
    'storeNginxConfigPath' => '', // Путь к дирректории где будут храниться конфиги
    'panelNginxDefaultConfigPath' => '', // Путь к дирректории где лежит файл default_config.conf
    'storeNginxDefaultConfigPath' => '', // Путь к дирректории где лежит файл default_config.conf

    'panelSqlPath' => '', // Путь к дампу базы данных соззданной панели
    'storeSqlPath' => Yii::getAlias('@sommerce/runtime/sql/store_template.sql'), // Путь к дампу базы данных созданного магазина

    'storeDefaultDatabase' => 'store_template', // Шаблонная база данных создаваемых магазинов

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
];
