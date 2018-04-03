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

    'invoice.domainDuration' => 7,
    'invoice.panelDuration' => 7,
    'invoice.sslDuration' => 7,
    'invoice.customDuration' => 7,
    'invoice.storeDuration' => 7,
];
