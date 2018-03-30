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
    'storeSqlPath' => '', // Путь к дампу базы данных соззданной магазина

    'panelDomain' => 'myperfectpanel.com',// Домен нашего сайта
    'storeDomain' => 'sommerce.net',// Домен нашего сайта

    'nginx_restart' => '/etc/init.d/nginx restart',

    // DNS параметры
    // адрес можно указать наш домен для отладки который возвращает успешное добавление всегда
    'dnsService' => 'https://api.cloudns.net',
    'dnsId' => '1181',
    'dnsPassword' => '2njujbuhwrZSgW96JynTN7JASe6Q8X64',
    'dnsIp' => $serverIp,
];
