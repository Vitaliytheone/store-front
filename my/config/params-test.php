<?php

return [
    'ddosGuardUrl' => 'http://mydev.perfectpanel.net/system/ddos-success',
    'dnsService' => 'http://mydev.perfectpanel.net',
    'testPayPal' => true,
    'testSSL' => true,

    // Ahnames auth data
    'ahnames.url' => 'https://demo-api.ahnames.com',
    'ahnames.login' => 'demo',
    'ahnames.password' => 'demo',

    'system.sslScriptUrl' => 'http://mydev.perfectpanel.net/nginx_config.php',
    'pending_orders' => 5000000, // Лимит создания доменов, панелей, ssl заказов
    'pending_tickets' => 5000000, // Лимит создания задач
    'whoisxml' => 0, // Проверяем или нет домен через whoisxml
];